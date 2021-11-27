<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql\Filter;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\ParameterizedTrait;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Expression\ExpressionInterface;

final class RangeContains extends CompareFilter
{
    use ParameterizedTrait;

    private ?string $rangeType = null;
    private ?string $valueType = null;

    /**
     * @param mixed $column
     * @param mixed $value
     */
    public function __construct($column, $value, ?string $table = null)
    {
        if (is_array($value) && count($value) !== 2) {
            throw new InvalidArgumentException('Value must be a [from, to] array.');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return 'pgsql.range_contains';
    }

    public function withRangeType(string $type): self
    {
        $new = clone $this;
        $new->rangeType = $type;

        return $new;
    }

    public function withValueType(string $type): self
    {
        $new = clone $this;
        $new->valueType = $type;

        return $new;
    }

    /**
     * @param mixed $value
     */
    private static function isEmpty($value): bool
    {
        return $value === null || $value === '';
    }

    public function toArray(): array
    {
        if ($this->value === null) {
            return parent::toArray();
        }

        $value = $this->value;
        $paramName = $this->getParamName();

        if (is_array($value)) {
            if (empty($this->rangeType)) {
                throw new RuntimeException('$rangeType must be set.');
            }

            $lower = array_shift($value);
            $upper = array_pop($value);
            $isLowerEmpty = self::isEmpty($lower);
            $isUpperEmpty = self::isEmpty($upper);

            if ($isLowerEmpty && $isUpperEmpty) {
                return [];
            }

            $expression = $this->rangeType . "(:lower_" . $paramName . ", :upper_" . $paramName . ", '[]')";
            $params = [
                ':lower_' . $paramName => $isLowerEmpty ? null : $lower,
                ':upper_' . $paramName => $isUpperEmpty ? null : $upper,
            ];

            return ['&&', $this->column, new Expression($expression, $params)];
        }

        if ($value instanceof ExpressionInterface === false) {
            if (empty($this->valueType)) {
                throw new RuntimeException('$valueType must be set.');
            }

            $value = new Expression(':' . $paramName . '::' . $this->valueType, [':' . $paramName => $value]);
        }

        return ['@>', $this->column, $value];
    }
}
