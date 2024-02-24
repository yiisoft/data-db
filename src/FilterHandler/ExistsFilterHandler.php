<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Db\Filter\Exists;
use Yiisoft\Data\Reader\FilterInterface;

final class ExistsFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Exists::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof Exists) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(['EXISTS', $filter->query]);
    }
}
