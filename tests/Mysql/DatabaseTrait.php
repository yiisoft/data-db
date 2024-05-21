<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Mysql\Connection;
use Yiisoft\Db\Mysql\Driver;

trait DatabaseTrait
{
    protected function makeConnection(): PdoConnectionInterface
    {
        $database = getenv('YII_MYSQL_DATABASE');
        $host = getenv('YII_MYSQL_HOST');
        $port = (int) getenv('YII_MYSQL_PORT');
        $user = getenv('YII_MYSQL_USER');
        $password = getenv('YII_MYSQL_PASSWORD');

        $pdoDriver = new Driver("mysql:host=$host;dbname=$database;port=$port", $user, $password);
        $pdoDriver->charset('UTF8MB4');

        return new Connection($pdoDriver, new SchemaCache(new ArrayCache()));
    }
}
