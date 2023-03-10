<?php

declare(strict_types=1);
namespace PhpPdo;

use PhpPdo\Driver\DriverInterface;
use PhpPdo\PdoInterface as PhpPdoInterface;
use PhpPdo\Driver\OdbcDriver;

class PhpPdo implements PhpPdoInterface
{

    private DriverInterface $driver;
    private int $errorMode = PdoInterface::ERRMODE_SILENT;

    public function __construct(string $dsn,
                                ?string $username = null,
                                ?string $password = null,
                                ?array $options = null)
    {
        $this->driver = new OdbcDriver($dsn, (string)$username, (string)$password);
    }

    public function beginTransaction(): bool
    {
        // TODO: Implement beginTransaction() method.
    }

    public function commit(): bool
    {
        // TODO: Implement commit() method.
    }

    public function errorCode(): ?string
    {
        // TODO: Implement errorCode() method.
    }

    public function errorInfo(): array
    {
        // TODO: Implement errorInfo() method.
    }

    public function exec(string $statement)
    {
        return $this->driver->exec($statement);
    }

    public function getAttribute(int $attribute): mixed
    {
        // TODO: Implement getAttribute() method.
    }

    public static function getAvailableDrivers(): array
    {
        // TODO: Implement getAvailableDrivers() method.
    }

    public function inTransaction(): bool
    {
        // TODO: Implement inTransaction() method.
    }

    public function lastInsertId(?string $name = null)
    {
        // TODO: Implement lastInsertId() method.
    }

    /**
     * @param string $query
     * @param array $options
     * @return false|PDOStatement
     * @throws \ReflectionException
     */
    public function prepare(string $query, array $options = [])
    {
        if (false === $statement = $this->driver->prepare($query)) return false;

        $reflectionClass = new \ReflectionClass('PhpPdo\PDOStatement');

        $result = $reflectionClass->newInstanceWithoutConstructor();

        $reflectionProperty = $reflectionClass->getProperty('driver');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($result, $this->driver);
        $reflectionProperty->setAccessible(false);

        $reflectionProperty = $reflectionClass->getProperty('handle');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($result, $statement);
        $reflectionProperty->setAccessible(false);

        return $result;
    }

    public function query(string $query, ?int $fetchMode = null)
    {
        // TODO: Implement query() method.
    }

    public function quote(string $string, int $type = PhpPdo::PARAM_STR)
    {
        // TODO: Implement quote() method.
    }

    public function rollBack(): bool
    {
        // TODO: Implement rollBack() method.
    }

    public function setAttribute(int $attribute, mixed $value): bool
    {
        // TODO: Implement setAttribute() method.
    }
}