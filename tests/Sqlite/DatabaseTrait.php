<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;
use Yiisoft\Test\Support\SimpleCache\MemorySimpleCache;

trait DatabaseTrait
{
    protected function makeConnection(): Connection
    {
        $db = new Connection(
            new Driver('sqlite::memory:'),
            new SchemaCache(new MemorySimpleCache()),
        );
        TestHelper::loadFixtures($db);
        return $db;
    }

    protected function getConnectionId(): string
    {
        return 'sqlite';
    }
}
