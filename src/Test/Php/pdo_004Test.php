<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PHPUnit\Framework\TestCase;

use PhpPdo\PhpPdo;

final class pdo_004Test extends TestCase
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

    public function test_FETCH_OBJ(): void
    {
        $db = new PhpPdo('pdo3');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, val VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES(1, 'A')");
        $db->exec("INSERT INTO test VALUES(2, 'B')");
        $db->exec("INSERT INTO test VALUES(3, 'C')");

        $stmt = $db->prepare('SELECT * from test');
        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_OBJ);

        $this->assertCount(3, $res);

        $this->assertInstanceOf('stdClass',$res[0]);
        $this->assertInstanceOf('stdClass',$res[1]);
        $this->assertInstanceOf('stdClass',$res[2]);

        $this->assertEquals(1,$res[0]->id);
        $this->assertEquals(2,$res[1]->id);
        $this->assertEquals(3,$res[2]->id);

        $this->assertEquals('A',$res[0]->val);
        $this->assertEquals('B',$res[1]->val);
        $this->assertEquals('C',$res[2]->val);

    }
}