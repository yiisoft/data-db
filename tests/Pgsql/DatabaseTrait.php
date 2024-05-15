<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql;

use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Pgsql\Connection;
use Yiisoft\Db\Pgsql\Driver;

trait DatabaseTrait
{
    protected function getConnection(): PdoConnectionInterface
    {
        $database = getenv('YII_PGSQL_DATABASE');
        $host = getenv('YII_PGSQL_HOST');
        $port = (int) getenv('YII_PGSQL_PORT');
        $user = getenv('YII_PGSQL_USER');
        $password = getenv('YII_PGSQL_PASSWORD');

        $pdoDriver = new Driver("pgsql:host=$host;dbname=$database;port=$port", $user, $password);
        $pdoDriver->charset('UTF8');

        return new Connection($pdoDriver, new SchemaCache(new ArrayCache()));
    }
}
