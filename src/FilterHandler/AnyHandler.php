<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Any;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

final class AnyHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Any::getOperator();
    }

    public function applyFilter(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        return $query->orWhere($filter->toCriteriaArray());
    }

    public function applyHaving(QueryInterface $query, FilterInterface $having): QueryInterface
    {
        return $query->orHaving($having->toCriteriaArray());
    }
}
