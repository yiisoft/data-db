<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use Yiisoft\Cache\ArrayCache;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;

trait DatabaseTrait
{
    protected function makeConnection(): PdoConnectionInterface
    {
        return new Connection(
            new Driver('sqlite::memory:'),
            new SchemaCache(new ArrayCache()),
        );
    }
}
