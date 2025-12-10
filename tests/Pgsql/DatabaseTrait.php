<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql;

use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Pgsql\Connection;
use Yiisoft\Db\Pgsql\Driver;
use Yiisoft\Test\Support\SimpleCache\MemorySimpleCache;

trait DatabaseTrait
{
    protected function makeConnection(): PdoConnectionInterface
    {
        $database = getenv('YII_PGSQL_DATABASE');
        $host = getenv('YII_PGSQL_HOST');
        $port = getenv('YII_PGSQL_PORT');
        $user = getenv('YII_PGSQL_USER');
        $password = getenv('YII_PGSQL_PASSWORD');

        $pdoDriver = new Driver("pgsql:host=$host;dbname=$database;port=$port", $user, $password);
        $pdoDriver->charset('UTF8');

        $db = new Connection($pdoDriver, new SchemaCache(new MemorySimpleCache()));

        TestHelper::loadFixtures($db);

        return $db;
    }

    protected function getConnectionId(): string
    {
        return 'pgsql';
    }
}
