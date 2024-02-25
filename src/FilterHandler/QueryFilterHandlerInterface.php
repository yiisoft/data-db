<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

interface QueryFilterHandlerInterface extends FilterHandlerInterface
{
    public function getCondition(FilterInterface $filter, Context $context): ?Condition;
}