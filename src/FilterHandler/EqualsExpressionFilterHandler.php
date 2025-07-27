<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\EqualsExpression;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsExpressionFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return self::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var EqualsExpression $filter */

        return new Criteria(['=', $filter->field, $filter->expression]);
    }
}
