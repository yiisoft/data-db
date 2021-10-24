<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

final class ILike extends Like
{
    public static function getOperator(): string
    {
        return 'ilike';
    }
}
