<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

abstract class CompareHandler implements QueryHandlerInterface
{
    public function applyFilter(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        $array = $filter->toCriteriaArray();

        if ($array === []) {
            return $query;
        }

        return $query->andWhere($array);
    }

    public function applyHaving(QueryInterface $query, FilterInterface $having): QueryInterface
    {
        $array = $having->toCriteriaArray();

        if ($array === []) {
            return $query;
        }

        return $query->having($array);
    }
}
