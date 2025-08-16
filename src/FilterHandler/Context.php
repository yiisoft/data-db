<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\FilterHandler;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

final class Context
{
    public function __construct(
        private readonly FilterHandler $filterHandler,
    ) {
    }

    public function handleFilter(FilterInterface $filter): ConditionInterface
    {
        return $this->filterHandler->handle($filter);
    }
}
