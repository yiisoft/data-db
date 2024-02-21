<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\EqualsNull as FilterEqualsNull;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

final class EqualsNull implements FilterInterface
{
    public function __construct(private readonly string|ExpressionInterface $column)
    {
    }

    public static function getOperator(): string
    {
        return FilterEqualsNull::getOperator();
    }

    public function toCriteriaArray(): array
    {
        return ['IS', $this->column, null];
    }
}
