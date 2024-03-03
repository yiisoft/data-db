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

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Like $filter */

        return new Criteria(['LIKE', $filter->getField(), $filter->getValue()]);
    }
}
