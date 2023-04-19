<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Iterator;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

interface QueryDataReaderInterface
{
    public function getPreparedQuery(): QueryInterface;

    public function withCountParam(?string $countParam): static;

    public function withHaving(?FilterInterface $having): static;

    public function withBatchSize(int $batchSize): static;
}
