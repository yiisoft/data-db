<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

final class OrILike extends MatchFilter
{
    public static function getOperator(): string
    {
        return 'or ilike';
    }
}
