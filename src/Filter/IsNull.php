<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Db\ColumnFormatterTrait;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

final class IsNull implements FilterInterface
{
    use ColumnFormatterTrait;

    public function __construct(string|ExpressionInterface $column, ?string $table = null)
    {
        $this->setColumn($column, $table);
    }

    public static function getOperator(): string
    {
        return 'is';
    }

    public function toCriteriaArray(): array
    {
        return [self::getOperator(), $this->column, null];
    }
}
