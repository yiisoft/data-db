<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\All as DbAll;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

final class AllHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return All::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        return new DbAll();
    }
}
