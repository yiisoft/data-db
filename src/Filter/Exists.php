<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Db\Query\Query;

final class Exists extends CompareFilter
{
    public function __construct(string $column, ?Query $value, ?string $table = null)
    {
        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return 'exists';
    }
}
