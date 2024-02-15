<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;

abstract class BaseHandler implements QueryHandlerInterface
{
    public function getCondition(FilterInterface $filter): ?array
    {
        return ConditionFactory::make($filter->toCriteriaArray());
    }
}
