<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Yiisoft\Data\Reader\FilterInterface;

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
    protected function createItem(array|object $row): array|object
    {
        /** @psalm-var TValue */
        return $row;
    }
}
