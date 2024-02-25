<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\EqualsNull;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsNullFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return EqualsNull::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var EqualsNull $filter */

        return new Condition(['IS', $filter->getField(), null]);
    }
}
