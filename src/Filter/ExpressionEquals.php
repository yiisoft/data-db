<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

final class ExpressionEquals implements FilterInterface
{
    public function __construct(
        public readonly string $field,
        public readonly ExpressionInterface $expression,
    ) {
    }
}
