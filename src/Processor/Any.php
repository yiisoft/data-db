<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Any as FilterAny;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Query\Query;

class Any implements QueryProcessorInterface
{
    public function getOperator(): string
    {
        return FilterAny::getOperator();
    }

    public function apply(Query $query, FilterInterface $filter): Query
    {
        return $query->orWhere($filter->toArray());
    }
}
