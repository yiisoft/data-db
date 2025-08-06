<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\FilterInterface;

final class AndXFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return AndX::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var AndX $filter */

        if (empty($filter->filters)) {
            return null;
        }

        $condition = ['AND'];
        $params = [];

        foreach ($filter->filters as $subFilter) {
            $criteria = $context->handleFilter($subFilter);
            if ($criteria !== null) {
                $condition[] = $criteria->condition;
                $params = array_merge($params, $criteria->params);
            }
        }
        return new Criteria($condition, $params);
    }
}
