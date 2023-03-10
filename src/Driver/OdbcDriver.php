<?php

declare(strict_types=1);
namespace PhpPdo\Driver;

use http\Exception\BadUrlException;
use PhpPdo\Driver\DriverInterface;

class OdbcDriver implements DriverInterface
{
    private $connection;
    public function __construct(string $dns, string $user = '', string $password = '')
    {
        $this->connection = odbc_connect(
            $dns,
            $user, $password);
    }

    public function exec(string $sql)
    {
        return @odbc_exec($this->connection, $sql);
    }

    public function errorCode($statement)
    {
    }
    public function errorInfo(): string
    {
        return odbc_errormsg($this->connection);
    }

    public function prepare(string $sql)
    {
        return odbc_prepare($this->connection, $sql);
    }


    public function fetchRow($statement)
    {
         return odbc_fetch_array($statement);
    }

    public function fetchFieds($statement): array
    {
        $fieldsCount = odbc_num_fields($statement);

        $fieds = [];
        for($i = 1; $i <= $fieldsCount; $i++)
        {
            $fieds[] = [
                'name'  => odbc_field_name($statement, $i),
                'type'  => odbc_field_type($statement, $i),
                'len'   => odbc_field_len($statement, $i),
                'scale' => odbc_field_scale($statement, $i),
            ];
        }
        return $fieds;
    }

}