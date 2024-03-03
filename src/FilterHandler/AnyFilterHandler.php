<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Any;
use Yiisoft\Data\Reader\FilterInterface;

final class AnyFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Any::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Any $filter */

        $filters = $filter->getFilters();
        if (empty($filters)) {
            return null;
        }

        $condition = ['OR'];
        $params = [];

        foreach ($filters as $subFilter) {
            $criteria = $context->handleFilter($subFilter);
            if ($criteria !== null) {
                $condition[] = $criteria->condition;
                $params = array_merge($params, $criteria->params);
            }
        }
        return new Criteria($condition, $params);
    }
}
