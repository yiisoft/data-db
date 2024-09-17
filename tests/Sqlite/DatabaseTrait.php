<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;
use Yiisoft\Db\Tests\Support\DbHelper;

use function dirname;

trait DatabaseTrait
{
    protected function getConnection(): PdoConnectionInterface
    {
        $db = new Connection(
            new Driver('sqlite::memory:'),
            new SchemaCache(new ArrayCache()),
        );

        DbHelper::loadFixture($db, dirname(__DIR__) . '/Support/Fixture/db.sql');

        return $db;
    }
}
