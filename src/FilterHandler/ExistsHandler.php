<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Exists;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Exists as DbExists;

final class ExistsHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Exists::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Exists $filter */

        return new DbExists($filter->query);
    }
}
