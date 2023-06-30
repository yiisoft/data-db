<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Like as FilterLike;

final class Like extends MatchFilter
{
    public static function getOperator(): string
    {
        return FilterLike::getOperator();
    }
}
