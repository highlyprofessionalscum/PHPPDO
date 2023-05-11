<?php

declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\Test\PhpPdoTestCase;

use PhpPdo\PhpPdo;

class pdo_010TestTest1
{
    public $id;
    public $val;

    private $construct = null;

    public function __construct()
    {
        $this->construct = 1;
    }
}

class pdo_010TestTest2
{
    public $id;
    public $val;

    private $construct = null;

    public function __construct()
    {
        $this->construct = 1;
    }
}

class pdo_010TestTest3
{
    public $id;
    public $val;
    private $construct = null;

    public function __construct()
    {
        $this->construct = 1;
    }
}


class pdo_010Test extends PhpPdoTestCase
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

    public function test_FETCH_CLASS_FETCH_CLASSTYPE_FETCH_UNIQUE(): void
    {
        $db = new PhpPdo('pdo3');

        $db->exec('CREATE TABLE classtypes(id int NOT NULL PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE)');
        $db->exec('INSERT INTO classtypes VALUES(0, "\\stdClass")');
        $db->exec('INSERT INTO classtypes VALUES(1, \'PhpPdo\\\Test\\\Php\\\pdo_010TestTest1\')');
        $db->exec('INSERT INTO classtypes VALUES(2, \'PhpPdo\\\Test\\\Php\\\pdo_010TestTest2\')');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, classtype int, val VARCHAR(10), grp VARCHAR(10))');
        $db->exec('INSERT INTO test VALUES(1, 0, \'A\', \'Group1\')');
        $db->exec('INSERT INTO test VALUES(2, 1, \'B\', \'Group1\')');
        $db->exec('INSERT INTO test VALUES(3, 2, \'C\', \'Group2\')');
        $db->exec('INSERT INTO test VALUES(4, 3, \'D\', \'Group2\')');

        $stmt = $db->prepare('SELECT classtypes.name, test.grp AS grp, test.id AS id, test.val AS val FROM test LEFT JOIN classtypes ON test.classtype=classtypes.id');
        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS|PhpPdo::FETCH_CLASSTYPE|PhpPdo::FETCH_GROUP, 'PhpPdo\Test\Php\pdo_010TestTest3');

        $this->assertInstanceOf('stdClass', $res['Group1'][0]);
        $this->assertInstanceOf('PhpPdo\Test\Php\pdo_010TestTest1', $res['Group1'][1]);

        $res = $stmt->fetchAll(PhpPdo::FETCH_CLASS|PhpPdo::FETCH_CLASSTYPE|PhpPdo::FETCH_UNIQUE, 'PhpPdo\Test\Php\pdo_010TestTest3');

        $this->assertInstanceOf('PhpPdo\Test\Php\pdo_010TestTest2', $res['Group2'][0]);
        $this->assertInstanceOf('PhpPdo\Test\Php\pdo_010TestTest3', $res['Group2'][1]);


    }
}