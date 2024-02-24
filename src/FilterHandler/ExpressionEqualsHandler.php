<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\ExpressionEquals;
use Yiisoft\Data\Reader\FilterInterface;

final class ExpressionEqualsHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return ExpressionEqualsHandler::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var ExpressionEquals $filter */
        return new Condition(['=', $filter->field, $filter->expression]);
    }
}
