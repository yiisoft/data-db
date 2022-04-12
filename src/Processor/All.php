<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\All as FilterAll;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

final class All implements QueryProcessorInterface
{
    public function getOperator(): string
    {
        return FilterAll::getOperator();
    }

    public function apply(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        return $query->andWhere($filter->toArray());
    }
}
