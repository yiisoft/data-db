<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler\LikeFilterHandler;

use RuntimeException;
use Yiisoft\Data\Reader\FilterHandlerInterface;

class LikeFilterHandlerFactory
{
    public static function getLikeHandler(string $driverName): FilterHandlerInterface
    {
        // default - ignored due to the complexity of testing and preventing splitting of databaseDriver argument.
        // @codeCoverageIgnoreStart
        return match ($driverName) {
            'sqlite' => new SqliteLikeFilterHandler(),
            'mysql' => new MysqlLikeFilterHandler(),
            'pgsql' => new PostgresLikeFilterHandler(),
            'sqlsrv' => new MssqlLikeFilterHandler(),
            'oci' => new OracleLikeFilterHandler(),
            default => throw new RuntimeException("$driverName database driver is not supported."),
        };
        // @codeCoverageIgnoreEnd
    }
}
