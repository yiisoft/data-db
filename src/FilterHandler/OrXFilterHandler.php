<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\OrX;
use Yiisoft\Data\Reader\FilterInterface;

final class OrXFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return OrX::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var OrX $filter */

        if (empty($filter->filters)) {
            return null;
        }

        $condition = ['OR'];
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
