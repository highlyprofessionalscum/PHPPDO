<?php
declare(strict_types=1);

namespace PhpPdo\Test\Php\PdoCompilant;
use PhpPdo\PhpPdo;
use PhpPdo\Test\PhpPdoTestCase;
use PHPUnit\Framework\TestCase;

class fetchUniqueTest extends PhpPdoTestCase
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

    public function testFetchUniue(): void
    {
        $db = new PhpPdo('pdo3');

        $db->exec("create table test
            (
                c1 varchar(100) null,
                c2 VARCHAR(100) null,
                c3 VARCHAR(100) null
            );");

        $db->exec("INSERT INTO test VALUES('ID-1','ID-1', 'Value 1')");
        $db->exec("INSERT INTO test VALUES('ID-2','ID-2', 'Value 2a')");
        $db->exec("INSERT INTO test VALUES('ID-2','ID-2', 'Value 2b')");
        $db->exec("INSERT INTO test VALUES('ID-3','ID-3', 'Value 3')");


        $stmt = $db->prepare('SELECT  c1, c2, c3 FROM test');
        $stmt->execute();

        $res = $stmt->fetchAll(PhpPdo::FETCH_ASSOC | PhpPdo::FETCH_UNIQUE);
        $this->assertSame('ID-1', $res['ID-1']['c2']);
        $this->assertSame('Value 1', $res['ID-1']['c3']);

        $this->assertSame('ID-2', $res['ID-2']['c2']);
        $this->assertSame('Value 2b', $res['ID-2']['c3']);

        $this->assertSame('ID-3', $res['ID-3']['c2']);
        $this->assertSame('Value 3', $res['ID-3']['c3']);
    }
}