<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

abstract class BaseHandler implements QueryHandlerInterface
{
    public function applyFilter(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        $condition = ConditionFactory::make($filter->toCriteriaArray());

        if ($condition === null) {
            return $query;
        }

        return $query->andWhere($condition);
    }

    public function applyHaving(QueryInterface $query, FilterInterface $having): QueryInterface
    {
        $condition = ConditionFactory::make($having->toCriteriaArray());

        if ($condition === null) {
            return $query;
        }

        return $query->andHaving($condition);
    }
}
