<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

/**
 * @template TKey as array-key
 * @template TValue as array|object
 *
 * @extends DataReaderInterface<TKey, TValue>
 */
interface QueryDataReaderInterface extends DataReaderInterface
{
    public function getPreparedQuery(): QueryInterface;

    public function withCountParam(?string $countParam): static;

    public function withHaving(FilterInterface $having): static;

    public function withBatchSize(?int $batchSize): static;
}
