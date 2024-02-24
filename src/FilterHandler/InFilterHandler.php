<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use LogicException;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\FilterInterface;

final class InFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return In::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var In $filter */

        return new Condition(['IN', $filter->field, $filter->getValues()]);
    }
}
