<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FieldMapper;

use Yiisoft\Db\Expression\ExpressionInterface;

final class ArrayFieldMapper implements FieldMapperInterface
{
    public function __construct(
        /**
         * @psalm-var array<string, string|ExpressionInterface>
         */
        private readonly array $map,
    ) {
    }

    public function map(string $field): string|ExpressionInterface
    {
        return $this->map[$field] ?? $field;
    }
}
