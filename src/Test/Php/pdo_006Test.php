<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PHPUnit\Framework\TestCase;

use PhpPdo\PhpPdo;



final class pdo_006Test extends TestCase
{
    public function setUp(): void
    {
        $db = new PhpPdo('pdo3');
        $test_tables = array(
            'test',
            'test2',
            'classtypes'
        );
        foreach ($test_tables as $table) {
            $db->exec("DROP TABLE $table");
        }
    }


    public function test_FETCH_GROUP(): void
    {
        $db = new PhpPdo('pdo3');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, val VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES(1, 'A')");
        $db->exec("INSERT INTO test VALUES(2, 'A')");
        $db->exec("INSERT INTO test VALUES(3, 'C')");

        $stmt = $db->prepare('SELECT val, id from test');

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_NUM|PhpPdo::FETCH_GROUP);
        $this->assertSame("1",  $res['A'][0][0]);
        $this->assertSame("2",  $res['A'][1][0]);
        $this->assertSame("3",  $res['C'][0][0]);

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_ASSOC|PhpPdo::FETCH_GROUP);
        $this->assertSame("1",  $res['A'][0]['id']);
        $this->assertSame("2",  $res['A'][1]['id']);
        $this->assertSame("3",  $res['C'][0]['id']);
    }
}