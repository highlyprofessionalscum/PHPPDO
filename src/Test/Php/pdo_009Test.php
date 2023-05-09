<?php

declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\Test\PhpPdoTestCase;

use PhpPdo\PhpPdo;

class Test1
{
    public $id;
    public $val;

    private $construct = null;

    public function __construct()
    {
        $this->construct = 1;
    }
}

class Test2
{
    public $id;
    public $val;

    private $construct = null;

    public function __construct()
    {
        $this->construct = 1;
    }
}

class Test3
{
    public $id;
    public $val;
    private $construct = null;

    public function __construct()
    {
        $this->construct = 1;
    }
}


class pdo_009Test extends PhpPdoTestCase
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

    public function test_FETCH_CLASSTYPE(): void
    {
        $db = new PhpPdo('pdo3');

        $db->exec('CREATE TABLE classtypes(id int NOT NULL PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE)');
        $db->exec('INSERT INTO classtypes VALUES(0, "\\stdClass")');
        $db->exec('INSERT INTO classtypes VALUES(1, \'PhpPdo\\\Test\\\Php\\\Test1\')');
        $db->exec('INSERT INTO classtypes VALUES(2, \'PhpPdo\\\Test\\\Php\\\Test2\')');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, classtype int, val VARCHAR(10))');
        $db->exec('INSERT INTO test VALUES(1, 0, \'A\')');
        $db->exec('INSERT INTO test VALUES(2, 1, \'B\')');
        $db->exec('INSERT INTO test VALUES(3, 2, \'C\')');
        $db->exec('INSERT INTO test VALUES(4, 3, \'D\')');

        $stmt = $db->prepare('SELECT classtypes.name, test.id AS id, test.val AS val FROM test LEFT JOIN classtypes ON test.classtype=classtypes.id');
        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_NUM);
        $this->assertSame('stdClass', $res[0][0]);
        $this->assertSame('1', $res[0][1]);
        $this->assertSame('A', $res[0][2]);

        $this->assertSame('PhpPdo\Test\Php\Test1', $res[1][0]);
        $this->assertSame('2', $res[1][1]);
        $this->assertSame('B', $res[1][2]);

        $this->assertSame('PhpPdo\Test\Php\Test2', $res[2][0]);
        $this->assertSame('3', $res[2][1]);
        $this->assertSame('C', $res[2][2]);

        $this->assertSame(null, $res[3][0]);
        $this->assertSame('4', $res[3][1]);
        $this->assertSame('D', $res[3][2]);

        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS|PhpPdo::FETCH_CLASSTYPE, 'PhpPdo\Test\Php\Test3');

        $this->assertInstanceOf('stdClass', $res[0]);
        $this->assertSame('1', $res[0]->id);
        $this->assertSame('A', $res[0]->val);

        $this->assertInstanceOf('PhpPdo\Test\Php\Test1', $res[1]);
        $this->assertSame('2', $res[1]->id);
        $this->assertSame('B', $res[1]->val);
        $this->assertSame(1, self::getProperty($res[1], 'construct'));

        $this->assertInstanceOf('PhpPdo\Test\Php\Test2', $res[2]);
        $this->assertSame('3', $res[2]->id);
        $this->assertSame('C', $res[2]->val);
        $this->assertSame(1, self::getProperty($res[2], 'construct'));

        $this->assertInstanceOf('PhpPdo\Test\Php\Test3', $res[3]);
        $this->assertSame('4', $res[3]->id);
        $this->assertSame('D', $res[3]->val);
        $this->assertSame(1, self::getProperty($res[3], 'construct'));
    }
}