<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Reader\Filter\FilterProcessorInterface;
use Yiisoft\Db\Query\Query;

interface QueryProcessorInterface extends FilterProcessorInterface
{
    public function apply(Query $query, FilterInterface $filter): Query;
}
