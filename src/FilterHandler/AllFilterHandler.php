<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\FilterInterface;

final class AllFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return All::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var All $filter */

        $filters = $filter->getFilters();
        if (empty($filters)) {
            return null;
        }

        $body = ['AND'];
        $params = [];

        foreach ($filters as $subFilter) {
            $condition = $context->handleFilter($subFilter);
            if ($condition !== null) {
                $body[] = $condition->body;
                $params = array_merge($params, $condition->params);
            }
        }
        return new Condition($body, $params);
    }
}
