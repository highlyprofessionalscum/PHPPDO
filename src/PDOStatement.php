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
    private const PDO_FETCH_FLAGS =0xFFFF0000;  /* fetchAll() modes or'd to PDO_FETCH_XYZ */

    public string $queryString;
    private $handle;
    private DriverInterface $driver;

    /**  */
    private ?array $fields = null; //  cache for query fields definition

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
        $flags = $mode & self::PDO_FETCH_FLAGS;
        $how   = $mode & ~self::PDO_FETCH_FLAGS;

        $result = [];
        while ($r = $this->fetchRowInternal($flags, $how, $second, $third)) {
            if (($flags & PhpPdo::FETCH_UNIQUE) === PhpPdo::FETCH_UNIQUE) {
                $result[array_shift($r)] = $r;
            }elseif ((PhpPdo::FETCH_GROUP & $flags)) {
                $f = array_shift($r);
                $result[$f[0]][] = $r;
            } else {
                $result[] = $r;
            }
        }

        return $result;
    }

    /**
     * @param int $mode
     * @param $second
     * @param $third
     * @return array|false|object
     */
    public function fetch(int $mode = PhpPdo::FETCH_DEFAULT, $second = null, $third = null)
    {
        return $this->fetchRowInternal($mode, $second, $third);
    }


    private function fetchRowInternal(int $flags, int $how, $second, $third)
    {
        $fields = $this->fetchFields();
        $r = $this->driver->fetchRow($this->handle);

        if (false === $r) {
            return false;
        }

        if (PhpPdo::FETCH_ASSOC === $how) {
            return $r;
        } elseif (PhpPdo::FETCH_NUM === $how) {
            return array_values($r);
        } elseif ((PhpPdo::FETCH_BOTH === $how) || (PhpPdo::FETCH_DEFAULT === $how)) {
            return array_merge($r, array_values($r));
        } elseif (PhpPdo::FETCH_OBJ === $how) {
            return (object)$r;
        } elseif (PhpPdo::FETCH_CLASS === $how) {
            if(null === $second){
                return (object)$r;
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