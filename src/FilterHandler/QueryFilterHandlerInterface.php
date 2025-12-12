<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

interface QueryFilterHandlerInterface
{
    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface;

    /**
     * Get matching filter class name.
     *
     * If the filter is active, a corresponding handler will be used during matching.
     *
     * @return string The filter class name.
     *
     * @psalm-return class-string<FilterInterface>
     */
    public function getFilterClass(): string;
}
