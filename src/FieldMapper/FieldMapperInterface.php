<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FieldMapper;

use Yiisoft\Db\Expression\ExpressionInterface;

/**
 * Maps field names to database column names or expressions.
 */
interface FieldMapperInterface
{
    /**
     * Maps a field name to a database column name or expression.
     *
     * @param string $field The field name to map.
     *
     * @return ExpressionInterface|string The mapped column name or expression.
     */
    public function map(string $field): string|ExpressionInterface;
}
