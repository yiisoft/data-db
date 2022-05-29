<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

abstract class CompareProcessor implements QueryProcessorInterface
{
    public function apply(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        $array = $filter->toArray();

        if ($array === []) {
            return $query;
        }

        return $query->andWhere($array);
    }
}
