<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PHPUnit\Framework\TestCase;

use PhpPdo\PhpPdo;

class pdo_007Test extends TestCase
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


    public function test_FETCH_UNIQUE(): void
    {
        $db = new PhpPdo('pdo3');

        $db->exec('CREATE TABLE test(id CHAR(1) NOT NULL PRIMARY KEY, val VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES('A', 'A')");
        $db->exec("INSERT INTO test VALUES('B', 'A')");
        $db->exec("INSERT INTO test VALUES('C', 'C')");

        $stmt = $db->prepare('SELECT id, val from test');

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_NUM | PhpPdo::FETCH_UNIQUE);

        $this->assertSame('A', $res['A'][0]);
        $this->assertSame('A', $res['B'][0]);
        $this->assertSame('C', $res['C'][0]);

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_ASSOC | PhpPdo::FETCH_UNIQUE);
        $this->assertSame('A', $res['A']['val']);
        $this->assertSame('A', $res['B']['val']);
        $this->assertSame('C', $res['C']['val']);

    }

}