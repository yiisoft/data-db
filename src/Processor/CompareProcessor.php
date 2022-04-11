<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

use function count;

abstract class CompareProcessor implements QueryProcessorInterface
{
    public function apply(QueryInterface $query, FilterInterface $filter): QueryInterface
    {
        $array = $filter->toArray();

        if (count($array) === 0) {
            return $query;
        }

        return $query->andWhere($array);
    }
}
