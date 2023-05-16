<?php

declare(strict_types=1);

namespace PhpPdo;

use \IteratorAggregate;
use PDOException;
use PhpPdo\Driver\DriverInterface;

/**
 * Represents a prepared statement and, after the statement is executed, an associated result set.
 */
class PDOStatement implements IteratorAggregate
{
    private const PDO_FETCH_FLAGS = 0xFFFF0000;  /* fetchAll() modes or'd to PDO_FETCH_XYZ */

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

        return $this->driver->execute($this->handle);
    }

    /**
     * @throws \ReflectionException
     */
    public function fetchAll(int $mode = PhpPdo::FETCH_DEFAULT, $second = null, $third = null): array
    {
        $flags = $mode & self::PDO_FETCH_FLAGS;
        $how = $mode & ~self::PDO_FETCH_FLAGS;

        $result = [];
        $group = null;
        while ($r = $this->fetchRowInternal($group, $flags, $how, $second, $third)) {
            if (($flags & PhpPdo::FETCH_UNIQUE) === PhpPdo::FETCH_UNIQUE) {
                $result[\array_shift($r)] = $r;
            } elseif ((PhpPdo::FETCH_GROUP & $flags)) {
                $result[$group][] = $r;
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
        $raw = [];
        $flags = $mode & self::PDO_FETCH_FLAGS;
        $how = $mode & ~self::PDO_FETCH_FLAGS;

        return $this->fetchRowInternal($raw, $flags, $how, $second, $third);
    }


    /**
     * @throws \ReflectionException
     */
    private function fetchRowInternal(&$group, int $flags, int $how, $second, $third)
    {
        // hot path
        $r = $this->driver->fetchAssoc($this->handle);

        if (false === $r) return false;

        if ((PhpPdo::FETCH_GROUP & $flags)) {
            if (($flags & PhpPdo::FETCH_UNIQUE) !== PhpPdo::FETCH_UNIQUE) {
                $group = \array_shift($r);
            }
        }

        if (PhpPdo::FETCH_ASSOC === $how) {
            return $r;
        } elseif (PhpPdo::FETCH_NUM === $how) {
            return array_values($r);
        } elseif ((PhpPdo::FETCH_BOTH === $how) || (PhpPdo::FETCH_DEFAULT === $how)) {
            return \array_merge($r, array_values($r));
        } elseif (PhpPdo::FETCH_OBJ === $how) {
            return (object)$r;
        }

        $fields = $this->fetchFields();

        if (PhpPdo::FETCH_CLASS === $how) {
            if (null === $second) {
                return (object)$r;
            }
            $className = $second;
            $constructorArgs = $third;
            if ($flags & PhpPdo::FETCH_CLASSTYPE) {

                if ((PhpPdo::FETCH_GROUP & $flags)) {
                    if (($flags & PhpPdo::FETCH_UNIQUE) !== PhpPdo::FETCH_UNIQUE) {
                        $className = $group;
                        $group = \array_shift($r);
                    }
                } else {
                    $className = \array_shift($r);
                }

                if (null === $className) {
                    $className = $second;
                }
            }
            return $this->fetchClass($fields, $r, $className, $constructorArgs);

        } elseif (PhpPdo::FETCH_FUNC === $how) {
            if (!is_callable($second)) {
                throw new PDOException('No fetch function specified');
            }

            if (\is_array($second)) {
                $res = \call_user_func_array($second, ...\array_values($r));
            } else {
                $res = $second(...array_values($r));
            }
            return $res;
        } else {
            throw new PDOException('Fetch mode must be a bitmask of PDO::FETCH_* constants');
        }

        throw new PDOException('Something wrong!');
    }

    /**
     * @throws \ReflectionException
     */
    private function fetchClass($fields, $r, ?string $class = 'stdClass', ?array $constructorArgs = null): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $class = $reflectionClass->newInstanceWithoutConstructor();

        $properties = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = 1;
        }

        foreach ($r as $field => $value) {
            if (\array_key_exists($field, $properties)) {
                $p = $reflectionClass->getProperty($field);

                if (\version_compare(PHP_VERSION, '8.1.0', '<')) {
                    $p->setAccessible(true);
                }

                $p->setValue($class, $value);

                if (\version_compare(PHP_VERSION, '8.1.0', '<')) {
                    $p->setAccessible(false);
                }
            } else {
                $class->{$field} = $value;
            }
        }

        if (method_exists($class, '__construct')) {
            if (null !== $constructorArgs) {
                $class->__construct(...$constructorArgs);
            } else {
                $class->__construct();
            }
        }

        return $class;
    }

    private function fetchFields(): array
    {
        if (null === $this->fields) {
            $this->fields = $this->driver->fetchFields($this->handle);
        }

        return $this->fields;
    }

    public function errorInfo(): string
    {
        return $this->driver->errorInfo($this->handle);
    }

    public function getIterator(): \Traversable
    {
        // TODO: Implement getIterator() method.
    }
}