<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Schema\Column\ColumnBuilder;

final class TestHelper
{
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
            ]
        )->execute();
        $db->createCommand()->batchInsert(
            'customer',
            ['id', 'email', 'name', 'address', 'status', 'profile_id'],
            [
                [1, 'user1@example.com', 'user1', 'address1', 1, 1],
                [2, 'user2@example.com', 'user2', 'address2', 1, null],
                [3, 'user3@example.com', 'user3', 'address3', 2, 2],
            ],
        )->execute();
    }
}
