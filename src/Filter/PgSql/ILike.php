<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter\PgSql;

use Yiisoft\Data\Db\Filter\Like;

class ILike extends Like
{
    public static function getOperator(): string
    {
        return 'ilike';
    }
}
