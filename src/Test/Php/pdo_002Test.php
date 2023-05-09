<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PhpPdo\Test\PhpPdoTestCase;
use PHPUnit\Framework\TestCase;

use PhpPdo\PhpPdo;

final class pdo_002Test extends PhpPdoTestCase
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

    public function test_FETCH_NUM(): void
    {
        $db = new PhpPdo('pdo3');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, val VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES(1, 'A')");
        $db->exec("INSERT INTO test VALUES(2, 'B')");
        $db->exec("INSERT INTO test VALUES(3, 'C')");

        $stmt = $db->prepare('SELECT * from test');
        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_NUM);

        $this->assertEquals([
            0 => ['0' => '1', '1' => 'A',],
            1 => ['0' => '2', '1' => 'B'],
            2 => ['0' => '3', '1' => 'C'],
        ], $res);

    }
}