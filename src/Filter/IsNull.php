<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Db\ColumnFormatterTrait;
use Yiisoft\Data\Reader\Filter\EqualsNull;
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
        return EqualsNull::getOperator();
    }

    public function toCriteriaArray(): array
    {
        return ['IS', $this->column, null];
    }
}
