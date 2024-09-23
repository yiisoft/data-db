<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Exception\Exception;
use Yiisoft\Db\Schema\SchemaInterface;

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
                'id' => $db->getSchema()->createColumn(SchemaInterface::TYPE_PK),
                'name' => $db->getSchema()->createColumn(SchemaInterface::TYPE_STRING, 128)->notNull(),
                'email' => $db->getSchema()->createColumn(SchemaInterface::TYPE_STRING, 128),
                'address' => $db->getSchema()->createColumn(SchemaInterface::TYPE_TEXT),
                'status' => $db->getSchema()->createColumn(SchemaInterface::TYPE_INTEGER)->defaultValue(0),
                'profile_id' => $db->getSchema()->createColumn(SchemaInterface::TYPE_INTEGER),
            ]
        )->execute();
        $db->createCommand()->batchInsert(
            'customer',
            ['email', 'name', 'address', 'status', 'profile_id'],
            [
                ['user1@example.com', 'user1', 'address1', 1, 1],
                ['user2@example.com', 'user2', 'address2', 1, null],
                ['user3@example.com', 'user3', 'address3', 2, 2],
            ],
        )->execute();
    }
}
