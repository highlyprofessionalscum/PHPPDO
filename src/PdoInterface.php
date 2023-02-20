<?php
namespace PhpPdo;
interface PdoInterface
{

    public const   PARAM_BOOL                        = 5;
    public const   PARAM_NULL                        = 0;
    public const   PARAM_INT                         = 1;
    public const   PARAM_STR                         = 2;
    public const   PARAM_LOB                         = 3;
    public const   PARAM_STMT                        = 4;
    public const   PARAM_INPUT_OUTPUT                = -2147483648;
    public const   PARAM_STR_NATL                    = 1073741824;
    public const   PARAM_STR_CHAR                    = 536870912;
    public const   PARAM_EVT_ALLOC                   = 0;
    public const   PARAM_EVT_FREE                    = 1;
    public const   PARAM_EVT_EXEC_PRE                = 2;
    public const   PARAM_EVT_EXEC_POST               = 3;
    public const   PARAM_EVT_FETCH_PRE               = 4;
    public const   PARAM_EVT_FETCH_POST              = 5;
    public const   PARAM_EVT_NORMALIZE               = 6;
    public const   FETCH_LAZY                        = 1;
    public const   FETCH_ASSOC                       = 2;
    public const   FETCH_NUM                         = 3;
    public const   FETCH_BOTH                        = 4;
    public const   FETCH_OBJ                         = 5;
    public const   FETCH_BOUND                       = 6;
    public const   FETCH_COLUMN                      = 7;
    public const   FETCH_CLASS                       = 8;
    public const   FETCH_INTO                        = 9;
    public const   FETCH_FUNC                        = 10;
    public const   FETCH_GROUP                       = 65536;
    public const   FETCH_UNIQUE                      = 196608;
    public const   FETCH_KEY_PAIR                    = 12;
    public const   FETCH_CLASSTYPE                   = 262144;
    public const   FETCH_SERIALIZE                   = 524288;
    public const   FETCH_PROPS_LATE                  = 1048576;
    public const   FETCH_NAMED                       = 11;
    public const   ATTR_AUTOCOMMIT                   = 0;
    public const   ATTR_PREFETCH                     = 1;
    public const   ATTR_TIMEOUT                      = 2;
    public const   ATTR_ERRMODE                      = 3;
    public const   ATTR_SERVER_VERSION               = 4;
    public const   ATTR_CLIENT_VERSION               = 5;
    public const   ATTR_SERVER_INFO                  = 6;
    public const   ATTR_CONNECTION_STATUS            = 7;
    public const   ATTR_CASE                         = 8;
    public const   ATTR_CURSOR_NAME                  = 9;
    public const   ATTR_CURSOR                       = 10;
    public const   ATTR_ORACLE_NULLS                 = 11;
    public const   ATTR_PERSISTENT                   = 12;
    public const   ATTR_STATEMENT_CLASS              = 13;
    public const   ATTR_FETCH_TABLE_NAMES            = 14;
    public const   ATTR_FETCH_CATALOG_NAMES          = 15;
    public const   ATTR_DRIVER_NAME                  = 16;
    public const   ATTR_STRINGIFY_FETCHES            = 17;
    public const   ATTR_MAX_COLUMN_LEN               = 18;
    public const   ATTR_EMULATE_PREPARES             = 20;
    public const   ATTR_DEFAULT_FETCH_MODE           = 19;
    public const   ATTR_DEFAULT_STR_PARAM            = 21;
    public const   ERRMODE_SILENT                    = 0;
    public const   ERRMODE_WARNING                   = 1;
    public const   ERRMODE_EXCEPTION                 = 2;
    public const   CASE_NATURAL                      = 0;
    public const   CASE_LOWER                        = 2;
    public const   CASE_UPPER                        = 1;
    public const   NULL_NATURAL                      = 0;
    public const   NULL_EMPTY_STRING                 = 1;
    public const   NULL_TO_STRING                    = 2;
    public const   ERR_NONE                          = "00000";
    public const   FETCH_ORI_NEXT                    = 0;
    public const   FETCH_ORI_PRIOR                   = 1;
    public const   FETCH_ORI_FIRST                   = 2;
    public const   FETCH_ORI_LAST                    = 3;
    public const   FETCH_ORI_ABS                     = 4;
    public const   FETCH_ORI_REL                     = 5;
    public const   CURSOR_FWDONLY                    = 0;
    public const   CURSOR_SCROLL                     = 1;
    public const   MYSQL_ATTR_USE_BUFFERED_QUERY     = 1000;
    public const   MYSQL_ATTR_LOCAL_INFILE           = 1001;
    public const   MYSQL_ATTR_INIT_COMMAND           = 1002;
    public const   MYSQL_ATTR_COMPRESS               = 1003;
    public const   MYSQL_ATTR_DIRECT_QUERY           = 1004;
    public const   MYSQL_ATTR_FOUND_ROWS             = 1005;
    public const   MYSQL_ATTR_IGNORE_SPACE           = 1006;
    public const   MYSQL_ATTR_SSL_KEY                = 1007;
    public const   MYSQL_ATTR_SSL_CERT               = 1008;
    public const   MYSQL_ATTR_SSL_CA                 = 1009;
    public const   MYSQL_ATTR_SSL_CAPATH             = 1010;
    public const   MYSQL_ATTR_SSL_CIPHER             = 1011;
    public const   MYSQL_ATTR_SERVER_PUBLIC_KEY      = 1012;
    public const   MYSQL_ATTR_MULTI_STATEMENTS       = 1013;
    public const   MYSQL_ATTR_SSL_VERIFY_SERVER_CERT = 1014;
    public const   PGSQL_ATTR_DISABLE_PREPARES       = 1000;
    public const   PGSQL_TRANSACTION_IDLE            = 0;
    public const   PGSQL_TRANSACTION_ACTIVE          = 1;
    public const   PGSQL_TRANSACTION_INTRANS         = 2;
    public const   PGSQL_TRANSACTION_INERROR         = 3;
    public const   PGSQL_TRANSACTION_UNKNOWN         = 4;
    public const   SQLITE_DETERMINISTIC              = 2048;
    public const   SQLITE_ATTR_OPEN_FLAGS            = 1000;
    public const   SQLITE_OPEN_READONLY              = 1;
    public const   SQLITE_OPEN_READWRITE             = 2;
    public const   SQLITE_OPEN_CREATE                = 4;
    public const   SQLITE_ATTR_READONLY_STATEMENT    = 1001;
    public const   SQLITE_ATTR_EXTENDED_RESULT_CODES = 1002;

    public const FETCH_DEFAULT = self::FETCH_BOTH;

    public function beginTransaction(): bool;

    public function commit(): bool;

    public function errorCode(): ?string;

    public function errorInfo(): array;

    public function exec(string $statement);

    public function getAttribute(int $attribute): mixed;

    public static function getAvailableDrivers(): array;

    public function inTransaction(): bool;

    public function lastInsertId(?string $name = null);

    public function prepare(string $query, array $options = []);

    public function query(string $query, ?int $fetchMode = null);

    public function quote(string $string, int $type = PhpPdo::PARAM_STR);

    public function rollBack(): bool;

    public function setAttribute(int $attribute, mixed $value): bool;
}