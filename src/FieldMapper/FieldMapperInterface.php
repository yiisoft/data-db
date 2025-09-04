<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FieldMapper;

use Yiisoft\Db\Expression\ExpressionInterface;

interface FieldMapperInterface
{
    public function map(string $field): string|ExpressionInterface;
}
