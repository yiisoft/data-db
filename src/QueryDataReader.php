<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

/**
 * Base class for `QueryDataReaderInterface`
 *
 * @template TKey as array-key
 * @template TValue as array
 *
 * @extends AbstractQueryDataReader<TKey, TValue>
 */
final class QueryDataReader extends AbstractQueryDataReader
{
    protected function createItem(array $row): array
    {
        /** @psalm-var TValue */
        return $row;
    }
}
