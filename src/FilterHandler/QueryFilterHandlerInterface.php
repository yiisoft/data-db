<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

interface QueryFilterHandlerInterface extends FilterHandlerInterface
{
    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface;
}
