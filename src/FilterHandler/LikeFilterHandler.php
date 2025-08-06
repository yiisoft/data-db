<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\LikeMode as DataLikeMode;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\LikeMode as DbLikeMode;

final class LikeFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Like::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Like $filter */

        return new Criteria([
            'LIKE',
            $filter->field,
            $filter->value,
            'caseSensitive' => $filter->caseSensitive,
            'mode' => $this->mapMode($filter->mode),
        ]);
    }

    public function mapMode(DataLikeMode $dataMode): DbLikeMode
    {
        return match ($dataMode) {
            DataLikeMode::Contains => DbLikeMode::Contains,
            DataLikeMode::StartsWith => DbLikeMode::StartsWith,
            DataLikeMode::EndsWith => DbLikeMode::EndsWith,
        };
    }
}
