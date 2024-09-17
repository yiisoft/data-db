<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle;

use PDO;
use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Oracle\Connection;
use Yiisoft\Db\Oracle\Driver;
use Yiisoft\Db\Tests\Support\DbHelper;

use function dirname;

trait DatabaseTrait
{
    protected function getConnection(): PdoConnectionInterface
    {
        $database = getenv('YII_ORACLE_DATABASE');
        $host = getenv('YII_ORACLE_HOST');
        $port = (int) getenv('YII_ORACLE_PORT');
        $user = getenv('YII_ORACLE_USER');
        $password = getenv('YII_ORACLE_PASSWORD');

        $pdoDriver = new Driver("oci:dbname=$host/XE;", $user, $password);
        $pdoDriver->charset('AL32UTF8');
        $pdoDriver->attributes([PDO::ATTR_STRINGIFY_FETCHES => true]);

        $db = new Connection($pdoDriver, new SchemaCache(new ArrayCache()));

        DbHelper::loadFixture($db, dirname(__DIR__) . '/Support/Fixture/db.sql');

        return $db;
    }
}
