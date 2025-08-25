<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\None;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\None as DbNone;

final class NoneHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return None::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        return new DbNone();
    }
}
