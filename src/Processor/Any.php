<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Any as FilterAny;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

final class Any implements QueryProcessorInterface
{
    public function getOperator(): string
    {
        return FilterAny::getOperator();
    }

    public function apply(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        return $query->orWhere($filter->toArray());
    }
}
