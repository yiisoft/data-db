<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\FilterInterface;

final class LikeFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Like::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var Like $filter */

        return new Condition(['LIKE', $filter->field, $filter->value]);
    }
}
