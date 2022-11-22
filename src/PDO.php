<?php

declare(strict_types=1);


use PHPPDO\Statement\PDOStatement;

class PDO implements PdoInterface
{

    private DriverInterface $driver;
    private int $errorMode = PdoInterface::ERRMODE_SILENT;

    public function __construct()
    {
        $this->driver = new OdbcDriver();
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

    public function exec(string $statement): int|false
    {
        // TODO: Implement exec() method.
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

    public function lastInsertId(?string $name = null): string|false
    {
        // TODO: Implement lastInsertId() method.
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        // TODO: Implement prepare() method.
    }

    public function query(string $query, ?int $fetchMode = null): PDOStatement|false
    {
        // TODO: Implement query() method.
    }

    public function quote(string $string, int $type = PDO::PARAM_STR): string|false
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