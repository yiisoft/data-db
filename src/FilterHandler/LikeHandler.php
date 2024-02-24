<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use LogicException;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\Not;
use Yiisoft\Data\Reader\FilterInterface;

final class LikeHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return Like::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof Like) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(['LIKE', $filter->field, $filter->value]);
    }
}
