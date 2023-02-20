<?php

declare(strict_types=1);

namespace PhpPdo;

use IteratorAggregate;
use PDOException;
use PhpPdo\Driver\DriverInterface;

/**
 * Represents a prepared statement and, after the statement is executed, an associated result set.
 */
class PDOStatement implements IteratorAggregate
{
    public string $queryString;
    private $handle;
    private DriverInterface $driver;

    private function __construct()
    {
    }

    public function execute(): bool
    {
        return odbc_execute($this->handle);
    }

    public function fetchAll(int $mode = PhpPdo::FETCH_DEFAULT): array
    {
        return $this->fetch($mode);
    }


    private function fetch($mode, $className = 'stdClass')
    {
        $result= [];
        $fields = $this->fetchFields();
        while ($r = $this->driver->fetchRow($this->handle)) {
            $temp = [];
            foreach ($fields as $fieldNum => $fieldValue) {
                if (PhpPdo::FETCH_ASSOC === $mode) {
                    $temp[$fieldValue['name']] = $r[$fieldValue['name']];
                } elseif (PhpPdo::FETCH_NUM === $mode) {
                    $temp[] = $r[$fieldValue['name']];
                } elseif ((PhpPdo::FETCH_BOTH === $mode) || (PhpPdo::FETCH_DEFAULT === $mode)) {
                    $temp[] = $temp[$fieldValue['name']] = $r[$fieldValue['name']];
                } elseif (PhpPdo::FETCH_OBJ === $mode) {
                    $temp = $this->fetchObject($r, $className);
                } else {
                    throw new PDOException('Wrong Mode!');
                }

            }

            $result[] = $temp;
        }

        return $result;
    }

    private function fetchFields(): array
    {
        return $this->driver->fetchFieds($this->handle);
    }

    public function errorInfo(): string
    {
        return $this->driver->errorInfo($this->handle);
    }

    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    private function fetchObject($r, $className = 'stdClass')
    {
        $obj = new $className();
        foreach ($r as $fieldName => $fieldValue) {
            $obj->$fieldName = $fieldValue;
        }

        return $obj;
    }
}