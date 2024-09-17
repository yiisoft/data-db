<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mssql;

use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Mssql\Connection;
use Yiisoft\Db\Mssql\Driver;
use Yiisoft\Db\Tests\Support\DbHelper;

use function dirname;

trait DatabaseTrait
{
    protected function getConnection(): PdoConnectionInterface
    {
        $database = getenv('YII_MSSQL_DATABASE');
        $host = getenv('YII_MSSQL_HOST');
        $port = (int) getenv('YII_MSSQL_PORT');
        $user = getenv('YII_MSSQL_USER');
        $password = getenv('YII_MSSQL_PASSWORD');

        $pdoDriver = new Driver("sqlsrv:Server=$host,$port;Database=$database", $user, $password);
        $pdoDriver->charset('UTF8MB4');

        $db = new Connection($pdoDriver, new SchemaCache(new ArrayCache()));

        DbHelper::loadFixture($db, dirname(__DIR__) . '/Support/Fixture/db.sql');

        return $db;
    }
}
