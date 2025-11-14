<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FieldMapper;

use Yiisoft\Db\Expression\ExpressionInterface;

/**
 * Maps field names using an array-based mapping configuration.
 *
 * This mapper uses a predefined array to map field names to database
 * column names or expressions. If a field is not found in the mapping,
 * it returns the original field name.
 */
final class ArrayFieldMapper implements FieldMapperInterface
{
    /**
     * @param (ExpressionInterface|string)[] $map The field mapping array where keys are field names and values are column names or expressions.
     * For example:
     *
     * ```php
     * [
     *     'name' => 'username',
     *     'jobId' => 'job_id',
     *     'profileData' => new Expression("data->>'profile'"),
     * ]
     *  ```
     *
     * @psalm-param array<string, string|ExpressionInterface> $map
     *
     * @example
     */
    public function __construct(
        private readonly array $map,
    ) {}

    public function map(string $field): string|ExpressionInterface
    {
        return $this->map[$field] ?? $field;
    }
}
