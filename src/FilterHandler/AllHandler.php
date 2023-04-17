<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\All;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

final class AllHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return All::getOperator();
    }

    public function applyFilter(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        return $query->andWhere($filter->toCriteriaArray());
    }

    public function applyHaving(QueryInterface $query, FilterInterface $having): QueryInterface
    {
        return $query->andHaving($having->toCriteriaArray());
    }
}
