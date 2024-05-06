<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Support;

use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;

trait TestTrait
{
    protected function getConnection(bool $fixture = false): PdoConnectionInterface
    {
        $db = new Connection(
            new Driver('sqlite::memory:'),
            DbHelper::getSchemaCache()
        );

        if ($fixture) {
            DbHelper::loadFixture($db, __DIR__ . '/Fixture/db.sql');
        }

        return $db;
    }
}
