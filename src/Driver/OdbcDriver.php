<?php

declare(strict_types=1);

namespace PhpPdo\Driver;

use PhpPdo\Driver\DriverInterface;

class OdbcDriver implements DriverInterface
{
    private $connection;

    public function __construct(string $dns, string $user = '', string $password = '')
    {
        $this->connection = \odbc_connect(
            $dns,
            $user, $password);
    }

    public function exec(string $sql)
    {
        return @\odbc_exec($this->connection, $sql);
    }

    public function errorCode(): string
    {
        return \odbc_error($this->connection);
    }

    public function errorInfo(): string
    {
        return \odbc_errormsg($this->connection);
    }

    public function prepare(string $sql)
    {
        return \odbc_prepare($this->connection, $sql);
    }


    public function fetchAssoc($statement, ?int $row_number = null)
    {
        return \odbc_fetch_array($statement, $row_number);
    }

    public function fetchNum($statement, ?int $row_number = null)
    {
        return \odbc_fetch_row($statement, $row_number);
    }

    public function fetchObj($statement, ?int $rownumber = null)
    {
        return \odbc_fetch_object($statement, $rownumber);
    }

    public function fetchFields($statement): array
    {
        $fieldsCount = \odbc_num_fields($statement);

        $fields = [];
        for ($i = 1; $i <= $fieldsCount; $i++) {
            $fields[] = [
                'name'  => \odbc_field_name($statement, $i),
                'type'  => \odbc_field_type($statement, $i),
                'len'   => \odbc_field_len($statement, $i),
                'scale' => \odbc_field_scale($statement, $i),
            ];
        }
        return $fields;
    }

    public function execute($statement): bool
    {
        return \odbc_execute($statement);
    }
}