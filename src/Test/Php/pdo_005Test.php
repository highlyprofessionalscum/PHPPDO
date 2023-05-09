<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PhpPdo\Test\PhpPdoTestCase;
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
    public static int $row = 0;
    public ?int $row2 = null;

    public function __construct(&$row)
    {
        if(null == self::$row){
            self::$row = $row;
        }

        self::$row++;
        $this->row2 =self::$row;
    }
}

final class pdo_005Test extends PhpPdoTestCase
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
        $this->assertInstanceOf('stdClass', $res[2]);
        $this->assertInstanceOf('stdClass', $res[2]);
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
        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS, 'PhpPdo\Test\Php\TestBase');

        $this->assertInstanceOf('PhpPdo\Test\Php\TestBase', $res[0]);
        $this->assertInstanceOf('PhpPdo\Test\Php\TestBase', $res[1]);
        $this->assertInstanceOf('PhpPdo\Test\Php\TestBase', $res[2]);

        $this->assertSame('1', $res[0]->id);
        $value = self::getProperty($res[0], 'val');
        $this->assertSame('A', $value);
        $value = self::getProperty($res[0], 'val2');
        $this->assertSame('AA', $value);

        $this->assertSame('2', $res[1]->id);
        $value = self::getProperty($res[1], 'val');
        $this->assertSame('B', $value);
        $value = self::getProperty($res[1], 'val2');
        $this->assertSame('BB', $value);


        $this->assertSame('3', $res[2]->id);
        $value = self::getProperty($res[2], 'val');
        $this->assertSame('C', $value);
        $value = self::getProperty($res[2], 'val2');
        $this->assertSame('CC', $value);


        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS, 'PhpPdo\Test\Php\TestDerived', array(100));

        $this->assertInstanceOf('PhpPdo\Test\Php\TestDerived', $res[0]);
        $this->assertInstanceOf('PhpPdo\Test\Php\TestDerived', $res[1]);
        $this->assertInstanceOf('PhpPdo\Test\Php\TestDerived', $res[2]);

        $value = self::getProperty($res[0], 'val');
        $this->assertSame('A', $value);
        $this->assertSame("AA",  $res[0]->val2);

        $value = self::getProperty($res[1], 'val');
        $this->assertSame('B', $value);
        $this->assertSame("BB",  $res[1]->val2);

        $value = self::getProperty($res[2], 'val');
        $this->assertSame('C', $value);
        $this->assertSame("CC",  $res[2]->val2);

        $this->assertSame(101,  $res[0]->row2);
        $this->assertSame(102,  $res[1]->row2);
        $this->assertSame(103,  $res[2]->row2);

    }
}