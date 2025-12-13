<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\QueryDataWriter;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Data\Writer\DataWriterException;

final class QueryDataWriterTest extends TestCase
{
    public function testWriteInserts(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
            'email' => $columnBuilder::text(),
        ])->execute();

        $writer = new QueryDataWriter($db, 'test', ['id'], false); // useUpsert = false

        $items = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
        ];

        $writer->write($items);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(2, $result);
        $this->assertSame('1', $result[0]['id']);
        $this->assertSame('John', $result[0]['name']);
        $this->assertSame('john@example.com', $result[0]['email']);
        $this->assertSame('2', $result[1]['id']);
        $this->assertSame('Jane', $result[1]['name']);
        $this->assertSame('jane@example.com', $result[1]['email']);
    }

    public function testWriteUpserts(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
            'email' => $columnBuilder::text(),
        ])->execute();

        // Insert initial data
        $db->createCommand()->insert('test', ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'])->execute();

        $writer = new QueryDataWriter($db, 'test', ['id'], true); // useUpsert = true

        $items = [
            ['id' => 1, 'name' => 'John Updated', 'email' => 'john.updated@example.com'], // Update
            ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'], // Insert
        ];

        $writer->write($items);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(2, $result);
        $this->assertSame('1', $result[0]['id']);
        $this->assertSame('John Updated', $result[0]['name']);
        $this->assertSame('john.updated@example.com', $result[0]['email']);
        $this->assertSame('2', $result[1]['id']);
        $this->assertSame('Jane', $result[1]['name']);
        $this->assertSame('jane@example.com', $result[1]['email']);
    }

    public function testWriteWithEmptyItems(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
        ])->execute();

        $writer = new QueryDataWriter($db, 'test');

        $writer->write([]);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(0, $result);
    }

    public function testWriteSkipsEmptyItem(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
        ])->execute();

        $writer = new QueryDataWriter($db, 'test');

        $items = [
            ['id' => 1, 'name' => 'John'],
            [], // This should be skipped
            ['id' => 2, 'name' => 'Jane'],
        ];

        $writer->write($items);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(2, $result);
        $this->assertSame('1', $result[0]['id']);
        $this->assertSame('2', $result[1]['id']);
    }

    public function testWriteThrowsExceptionOnInvalidItem(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
        ])->execute();

        $writer = new QueryDataWriter($db, 'test');

        $this->expectException(DataWriterException::class);
        $this->expectExceptionMessage('Each item must be an array.');

        $writer->write(['not an array']);
    }

    public function testDelete(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
        ])->execute();

        $data = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
            ['id' => 3, 'name' => 'Bob'],
        ];
        $db->createCommand()->insertBatch('test', $data)->execute();

        $writer = new QueryDataWriter($db, 'test');

        $itemsToDelete = [
            ['id' => 1],
            ['id' => 3],
        ];

        $writer->delete($itemsToDelete);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(1, $result);
        $this->assertSame('2', $result[0]['id']);
        $this->assertSame('Jane', $result[0]['name']);
    }

    public function testDeleteWithCompositeKey(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id1' => $columnBuilder::integer()->notNull(),
            'id2' => $columnBuilder::integer()->notNull(),
            'name' => $columnBuilder::text(),
        ])->execute();
        $db->createCommand()->addPrimaryKey('test', 'pk', ['id1', 'id2'])->execute();

        $data = [
            ['id1' => 1, 'id2' => 1, 'name' => 'John'],
            ['id1' => 1, 'id2' => 2, 'name' => 'Jane'],
            ['id1' => 2, 'id2' => 1, 'name' => 'Bob'],
        ];
        $db->createCommand()->insertBatch('test', $data)->execute();

        $writer = new QueryDataWriter($db, 'test', ['id1', 'id2']);

        $itemsToDelete = [
            ['id1' => 1, 'id2' => 1],
            ['id1' => 2, 'id2' => 1],
        ];

        $writer->delete($itemsToDelete);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(1, $result);
        $this->assertSame('1', $result[0]['id1']);
        $this->assertSame('2', $result[0]['id2']);
        $this->assertSame('Jane', $result[0]['name']);
    }

    public function testDeleteWithEmptyItems(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
        ])->execute();

        $db->createCommand()->insert('test', ['id' => 1, 'name' => 'John'])->execute();

        $writer = new QueryDataWriter($db, 'test');

        $writer->delete([]);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(1, $result);
    }

    public function testDeleteSkipsEmptyItem(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
        ])->execute();

        $data = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane'],
        ];
        $db->createCommand()->insertBatch('test', $data)->execute();

        $writer = new QueryDataWriter($db, 'test');

        $itemsToDelete = [
            ['id' => 1],
            [], // This should be skipped
        ];

        $writer->delete($itemsToDelete);

        $result = $db->createQuery()->from('test')->all();
        $this->assertCount(1, $result);
        $this->assertSame('2', $result[0]['id']);
    }

    public function testDeleteThrowsExceptionOnInvalidItem(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
        ])->execute();

        $writer = new QueryDataWriter($db, 'test');

        $this->expectException(DataWriterException::class);
        $this->expectExceptionMessage('Each item must be an array.');

        $writer->delete(['not an array']);
    }

    public function testDeleteThrowsExceptionOnMissingPrimaryKey(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', [
            'id' => $columnBuilder::integer()->notNull()->primaryKey(),
            'name' => $columnBuilder::text(),
        ])->execute();

        $db->createCommand()->insert('test', ['id' => 1, 'name' => 'John'])->execute();

        $writer = new QueryDataWriter($db, 'test');

        $this->expectException(DataWriterException::class);
        $this->expectExceptionMessage('Item must contain primary key column "id" for deletion.');

        $writer->delete([['name' => 'John']]);
    }
}
