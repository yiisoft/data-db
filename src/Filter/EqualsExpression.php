<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

final class EqualsExpression implements FilterInterface
{
    public function __construct(
        private readonly string $field,
        private readonly ExpressionInterface $expression,
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }
}
