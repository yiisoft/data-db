<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle;

use PDO;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Oracle\Connection;
use Yiisoft\Db\Oracle\Driver;
use Yiisoft\Test\Support\SimpleCache\MemorySimpleCache;

trait DatabaseTrait
{
    protected function makeConnection(): PdoConnectionInterface
    {
        $database = getenv('YII_ORACLE_DATABASE');
        $host = getenv('YII_ORACLE_HOST');
        $port = getenv('YII_ORACLE_PORT');
        $user = getenv('YII_ORACLE_USER');
        $password = getenv('YII_ORACLE_PASSWORD');

        $pdoDriver = new Driver("oci:dbname=//$host:$port/$database", $user, $password);
        $pdoDriver->charset('AL32UTF8');
        $pdoDriver->attributes([PDO::ATTR_STRINGIFY_FETCHES => true]);

        $db = new Connection($pdoDriver, new SchemaCache(new MemorySimpleCache()));

        TestHelper::loadFixtures($db);

        return $db;
    }

    protected function getConnectionId(): string
    {
        return 'oracle';
    }
}
