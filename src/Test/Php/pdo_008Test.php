<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\Test\PhpPdoTestCase;

use PhpPdo\PhpPdo;

class pdo_008Test extends PhpPdoTestCase
{

    public function test_FETCH_UNIQUE(): void
    {
        $db = new PhpPdo('pdo3');

        $db->exec('CREATE TABLE test(id CHAR(1) NOT NULL PRIMARY KEY, val VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES('A', 'A')");
        $db->exec("INSERT INTO test VALUES('B', 'A')");
        $db->exec("INSERT INTO test VALUES('C', 'C')");

        $stmt = $db->prepare('SELECT val, id from test');

        $stmt->execute();
        $res = $stmt->fetchAll(PhpPdo::FETCH_NUM | PhpPdo::FETCH_UNIQUE);

        $this->assertSame('B', $res['A'][0]);
        $this->assertSame('C', $res['D'][0]);

    }

}