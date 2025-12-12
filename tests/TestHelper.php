<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use Yiisoft\Data\Db\FieldMapper\ArrayFieldMapper;
use Yiisoft\Data\Db\FilterHandler;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AndXHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
use Yiisoft\Data\Db\FilterHandler\Context;
use Yiisoft\Data\Db\FilterHandler\EqualsExpressionHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsNullHandler;
use Yiisoft\Data\Db\FilterHandler\ExistsHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\InHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\LikeHandler;
use Yiisoft\Data\Db\FilterHandler\NoneHandler;
use Yiisoft\Data\Db\FilterHandler\NotHandler;
use Yiisoft\Data\Db\FilterHandler\OrXHandler;
use Yiisoft\Db\Cache\SchemaCache;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Schema\Column\ColumnBuilder;
use Yiisoft\Db\Sqlite\Connection;
use Yiisoft\Db\Sqlite\Driver;
use Yiisoft\Test\Support\SimpleCache\MemorySimpleCache;

final class TestHelper
{
    public static function createSqliteConnection(): Connection
    {
        return new Connection(
            new Driver('sqlite::memory:'),
            new SchemaCache(new MemorySimpleCache()),
        );
    }

    public static function createContext(): Context
    {
        $fieldMapper = new ArrayFieldMapper([]);
        return new Context(
            new FilterHandler(
                [
                    new AllHandler(),
                    new NoneHandler(),
                    new AndXHandler(),
                    new OrXHandler(),
                    new EqualsHandler(),
                    new GreaterThanHandler(),
                    new GreaterThanOrEqualHandler(),
                    new LessThanHandler(),
                    new LessThanOrEqualHandler(),
                    new LikeHandler(),
                    new InHandler(),
                    new ExistsHandler(),
                    new NotHandler(),
                    new BetweenHandler(),
                    new EqualsNullHandler(),
                    new EqualsExpressionHandler(),
                ],
                $fieldMapper,
            ),
            $fieldMapper,
        );
    }

    public static function loadFixtures(ConnectionInterface $db): void
    {
        try {
            $db->createCommand()->dropTable('customer')->execute();
        } catch (Exception) {
        }
        $db->createCommand()->createTable(
            'customer',
            [
                'id' => ColumnBuilder::integer()->notNull(),
                'name' => ColumnBuilder::string(128)->notNull(),
                'email' => ColumnBuilder::string(128),
                'address' => ColumnBuilder::text(),
                'status' => ColumnBuilder::integer()->defaultValue(0),
                'profile_id' => ColumnBuilder::integer(),
            ],
        )->execute();
        $db->createCommand()->insertBatch(
            'customer',
            [
                [1, 'user1@example.com', 'user1', 'address1', 1, 1],
                [2, 'user2@example.com', 'user2', 'address2', 1, null],
                [3, 'user3@example.com', 'user3', 'address3', 2, 2],
            ],
            ['id', 'email', 'name', 'address', 'status', 'profile_id'],
        )->execute();
    }
}
