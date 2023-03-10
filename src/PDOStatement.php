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

    /**  */
    private ?array $fields = null;

    private function __construct()
    {
    }

    public function execute(): bool
    {
        $this->fields = null;

        return odbc_execute($this->handle);
    }

    public function fetchAll(int $mode = PhpPdo::FETCH_DEFAULT, $second = null, $third = null): array
    {
        $result = [];
        while ($r = $this->fetchRowInternal($mode, $second, $third)) {
            $result[] = $r;
        }

        return $result;
    }

    public function fetch(int $mode = PhpPdo::FETCH_DEFAULT, $second = null, $third = null): array
    {
        return $this->fetchRowInternal($mode, $second, $third);
    }


    private function fetchRowInternal(int $mode, $second, $third)
    {
        $temp = [];
        $fields = $this->fetchFields();
        while ($r = $this->driver->fetchRow($this->handle)) {

            foreach ($fields as $fieldNum => $fieldValue) {
                if (PhpPdo::FETCH_ASSOC === $mode) {
                    $temp[$fieldValue['name']] = $r[$fieldValue['name']];
                } elseif (PhpPdo::FETCH_NUM === $mode) {
                    $temp[] = $r[$fieldValue['name']];
                } elseif ((PhpPdo::FETCH_BOTH === $mode) || (PhpPdo::FETCH_DEFAULT === $mode)) {
                    $temp[] = $temp[$fieldValue['name']] = $r[$fieldValue['name']];
                } elseif (PhpPdo::FETCH_OBJ === $mode) {
                    $temp = $this->fetchObject($r);
                } elseif (PhpPdo::FETCH_CLASS=== $mode) {
                    $class = $second ?: 'stdClass';
                    $temp = $this->fetchClass($fieldValue['name'], $r[$fieldValue['name']], $class, $third);
                } else {
                    throw new PDOException('Wrong Mode!');
                }

            }

            break;
        }

        return $temp;
    }

    private function fetchClass($fieldname,, string $class, ?array $constructorArgs): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $class = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($r as $item) {

        }
        $reflectionProperty = $reflectionClass->getProperty('driver');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($result, $this->driver);
        $reflectionProperty->setAccessible(false);

        if (null !== $constructorArgs) {
            $class->__construct($constructorArgs);
        }

        return $class;
    }

    private function fetchFields(): array
    {
        if (null === $this->fields) {
            $this->fields = $this->driver->fetchFieds($this->handle);
        }
        return $this->fields;
    }

    public function errorInfo(): string
    {
        return $this->driver->errorInfo($this->handle);
    }

    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    private function fetchObject($r)
    {
        $obj = new \stdClass();
        foreach ($r as $fieldName => $fieldValue) {
            $obj->$fieldName = $fieldValue;
        }

        return $obj;
    }
}