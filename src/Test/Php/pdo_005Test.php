<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PHPUnit\Framework\TestCase;

use PhpPdo\PhpPdo;

class TestBase
{
    public $id;
    protected $val;
    private $val2;
}

#[AllowDynamicProperties] // $val2 will be dynamic now.
class TestDerived extends TestBase
{
    protected $row;

    public function __construct(&$row)
    {
        echo __METHOD__ . "($row,{$this->id})\n";
        $this->row = $row++;
    }
}

final class pdo_005Test extends TestCase
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

    public function test_FETCH_CLASS(): void
    {
        $db = new PhpPdo('pdo3');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, val VARCHAR(10), val2 VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES(1, 'A', 'AA')");
        $db->exec("INSERT INTO test VALUES(2, 'B', 'BB')");
        $db->exec("INSERT INTO test VALUES(3, 'C', 'CC')");

        $stmt = $db->prepare('SELECT * from test');

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS);

        $this->assertInstanceOf('stdClass', $res[0]);
        $this->assertSame("1",  $res[0]->id);
        $this->assertSame("A",  $res[0]->val);
        $this->assertSame("AA", $res[0]->val2);
        $this->assertSame("2",  $res[1]->id);
        $this->assertSame("B",  $res[1]->val);
        $this->assertSame("BB", $res[1]->val2);
        $this->assertSame("3",  $res[2]->id);
        $this->assertSame("C",  $res[2]->val);
        $this->assertSame("CC", $res[2]->val2);


        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS, 'TestBase');

        $this->assertInstanceOf('TestBase', $res[0]);
        $this->assertSame('2', $res[0]->id);

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS, 'TestDerived', array(0));

        $this->assertInstanceOf('TestDerived', $res[0]);

    }
}