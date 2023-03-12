<?php

declare(strict_types=1);

namespace PhpPdo;

use IteratorAggregate;
use PDOException;
use PhpPdo\Driver\DriverInterface;
use PhpPdo\Test\Php\TestBase;
use PHPUnit\Util\Test;

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
        echo __METHOD__ . "($row,{$this->id})\n";
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
        $fields = $this->fetchFields();
        $r = $this->driver->fetchRow($this->handle);

        if (false === $r) {
            return false;
        }

        if (PhpPdo::FETCH_ASSOC === $mode) {
            return $r;
        } elseif (PhpPdo::FETCH_NUM === $mode) {
            return array_values($r);
        } elseif ((PhpPdo::FETCH_BOTH === $mode) || (PhpPdo::FETCH_DEFAULT === $mode)) {
            return array_merge($r, array_values($r));
        } elseif (PhpPdo::FETCH_OBJ === $mode) {
            return (object)$r;
        } elseif (PhpPdo::FETCH_CLASS === $mode) {
            if(null === $second){
                $second = '\stdClass';
            }
            return $this->fetchClass($fields, $r, $second, $third);
        } else {
            throw new PDOException('Wrong Mode!');
        }

        throw new PDOException('Something wrong!');
    }

    private function fetchClass($fields, $r, ?string $class = 'stdClass', ?array $constructorArgs = null): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $class = $reflectionClass->newInstanceWithoutConstructor();

        $properties = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = 1;
        }

        foreach ($r as $field => $value) {
            if (array_key_exists($field, $properties)) {
                $p = $reflectionClass->getProperty($field);

                if (version_compare(PHP_VERSION, '8.1.0', '<')) {
                    $p->setAccessible(true);
                }

                $p->setValue($class, $value);

                if (version_compare(PHP_VERSION, '8.1.0', '<')) {
                    $p->setAccessible(false);
                }
            } else {
                $class->{$field} = $value;
            }
        }

        if (null !== $constructorArgs) {
            $class->__construct(...$constructorArgs);
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
}