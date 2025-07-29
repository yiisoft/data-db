<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\FilterInterface;

final class InFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return In::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var In $filter */

        return new Criteria(['IN', $filter->field, $filter->values]);
    }
}
