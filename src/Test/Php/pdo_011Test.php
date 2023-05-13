<?php

declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\Test\PhpPdoTestCase;

use PhpPdo\PhpPdo;
use function PHPUnit\Framework\assertSame;

function test($id, $val = 'N/A')
{
    return array($id => $val);
}

class pdo_011Test extends PhpPdoTestCase
{
    public function test_FETCH(): void
    {
        $db = new PhpPdo('pdo3');

        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, val VARCHAR(100), grp VARCHAR(10))');
        $db->exec('INSERT INTO test VALUES(1, \'A\', \'Group1\')');
        $db->exec('INSERT INTO test VALUES(2, \'B\', \'Group1\')');
        $db->exec('INSERT INTO test VALUES(3, \'C\', \'Group2\')');
        $db->exec('INSERT INTO test VALUES(4, \'D\', \'Group2\')');

        $select1 = $db->prepare('SELECT grp, id FROM test');
        $select2 = $db->prepare('SELECT id, val FROM test');
        $derived = $db->prepare('SELECT id, val FROM test', array(PhpPdo::ATTR_STATEMENT_CLASS => array('DerivedStatement', array('Overloaded', $db))));


        $select1->execute();
        $res = $select1->fetchAll(PhpPdo::FETCH_FUNC|PhpPdo::FETCH_GROUP, 'PhpPdo\Test\Php\test');
        $this->assertSame('1',$res['Group1'][0][0]);
        $this->assertSame('N/A',$res['Group1'][0][1]);

        $this->assertSame('2',$res['Group1'][1][0]);
        $this->assertSame('N/A',$res['Group1'][1][0]);

        $this->assertSame('3',$res['Group2'][0][0]);
        $this->assertSame('N/A',$res['Group2'][0][1]);

        $this->assertSame('4',$res['Group2'][1][0]);
        $this->assertSame('N/A',$res['Group2'][1][0]);
    }
}