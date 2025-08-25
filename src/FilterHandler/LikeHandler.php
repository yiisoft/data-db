<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\LikeMode;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Like as DbLikeCondition;
use Yiisoft\Db\QueryBuilder\Condition\LikeMode as DbLikeMode;

final class LikeHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Like::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Like $filter */

        return new DbLikeCondition(
            $filter->field,
            $filter->value,
            $filter->caseSensitive,
            mode: $this->mapMode($filter->mode),
        );
    }

    public function mapMode(LikeMode $dataMode): DbLikeMode
    {
        return match ($dataMode) {
            LikeMode::Contains => DbLikeMode::Contains,
            LikeMode::StartsWith => DbLikeMode::StartsWith,
            LikeMode::EndsWith => DbLikeMode::EndsWith,
        };
    }
}
