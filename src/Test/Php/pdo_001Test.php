<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php;

use PhpPdo\PdoInterface;
use PhpPdo\Test\PhpPdoTestCase;
use PHPUnit\Framework\TestCase;

use PhpPdo\PhpPdo;
use function PHPUnit\Framework\assertEquals;

final class pdo_001Test extends PhpPdoTestCase
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

    public function test_FETCH_ASSOC(): void
    {
        $db = new PhpPdo('pdo3');
        $db->exec('CREATE TABLE test(id int NOT NULL PRIMARY KEY, val VARCHAR(10))');
        $db->exec("INSERT INTO test VALUES(1, 'A')");
        $db->exec("INSERT INTO test VALUES(2, 'B')");
        $db->exec("INSERT INTO test VALUES(3, 'C')");

        $stmt = $db->prepare('SELECT * from test');
        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_ASSOC);

        $this->assertEquals([
            0 => ['id' => '1', 'val' => 'A',],
            1 => ['id' => '2', 'val' => 'B'],
            2 => ['id' => '3', 'val' => 'C'],
        ], $res);
    }
}