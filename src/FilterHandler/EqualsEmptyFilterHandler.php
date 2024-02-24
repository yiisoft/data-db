<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\EqualsEmpty;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsEmptyFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return EqualsEmpty::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var EqualsEmpty $filter */

        return new Condition(['OR', ['IS', $filter->field, null], ['=', $filter->field, '']]);
    }
}
