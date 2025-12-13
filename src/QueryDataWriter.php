<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Throwable;
use Yiisoft\Data\Writer\DataWriterException;
use Yiisoft\Data\Writer\DataWriterInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

use function is_array;

/**
 * QueryDataWriter allows writing (inserting/updating) and deleting data to/from a database table.
 *
 * @template TKey as array-key
 * @template TValue as array
 *
 * @implements DataWriterInterface<TKey, TValue>
 *
 * Example usage:
 *
 * ```php
 * $writer = new QueryDataWriter($db, 'customer');
 *
 * // Write (insert or update) items
 * $writer->write([
 *     ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
 *     ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
 * ]);
 *
 * // Delete items
 * $writer->delete([
 *     ['id' => 1],
 *     ['id' => 2],
 * ]);
 * ```
 *
 * @psalm-suppress ClassMustBeFinal This class can be extended.
 */
class QueryDataWriter implements DataWriterInterface
{
    /**
     * @param ConnectionInterface $db The database connection instance.
     * @param string $table The name of the table to write to or delete from.
     * @param array<string> $primaryKey The primary key column name(s). Defaults to ['id'].
     * @param bool $useUpsert Whether to use UPSERT (insert or update) instead of plain INSERT.
     *        When true, existing records will be updated, otherwise only new records will be inserted.
     *        Defaults to true.
     */
    public function __construct(
        private readonly ConnectionInterface $db,
        private readonly string $table,
        private readonly array $primaryKey = ['id'],
        private readonly bool $useUpsert = true,
    ) {
    }

    /**
     * Write items to the database table.
     *
     * If `$useUpsert` is true (default), this will insert new records or update existing ones.
     * If `$useUpsert` is false, this will only insert new records.
     *
     * @param iterable $items Items to write. Each item must be an associative array of column => value.
     *
     * @throws DataWriterException If there is an error while writing items.
     */
    public function write(iterable $items): void
    {
        try {
            foreach ($items as $item) {
                if (!is_array($item)) {
                    throw new DataWriterException('Each item must be an array.');
                }

                if (empty($item)) {
                    continue;
                }

                if ($this->useUpsert) {
                    $this->db->createCommand()->upsert($this->table, $item)->execute();
                } else {
                    $this->db->createCommand()->insert($this->table, $item)->execute();
                }
            }
        } catch (Throwable $e) {
            throw new DataWriterException(
                'Failed to write items to table "' . $this->table . '": ' . $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    /**
     * Delete items from the database table.
     *
     * Each item should contain the primary key column(s) used to identify the record to delete.
     *
     * @param iterable $items Items to delete. Each item must be an associative array containing at least
     *        the primary key column(s).
     *
     * @throws DataWriterException If there is an error deleting items.
     */
    public function delete(iterable $items): void
    {
        try {
            foreach ($items as $item) {
                if (!is_array($item)) {
                    throw new DataWriterException('Each item must be an array.');
                }

                if (empty($item)) {
                    continue;
                }

                $condition = [];
                foreach ($this->primaryKey as $key) {
                    if (!isset($item[$key])) {
                        throw new DataWriterException(
                            'Item must contain primary key column "' . $key . '" for deletion.',
                        );
                    }
                    $condition[$key] = $item[$key];
                }

                $this->db->createCommand()->delete($this->table, $condition)->execute();
            }
        } catch (DataWriterException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataWriterException(
                'Failed to delete items from table "' . $this->table . '": ' . $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }
}
