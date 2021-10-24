<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;

final class Between extends CompareFilter
{
    /**
     * @param mixed $column
     */
    public function __construct($column, ?array $value, ?string $table = null)
    {
        if (is_array($value) && count($value) !== 2) {
            throw new InvalidArgumentException('Value must be an array with 2 elements');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return 'between';
    }

    /**
     * @param mixed $value
     */
    private static function isEmpty($value): bool
    {
        return $value !== null && $value !== '';
    }

    public function toArray(): array
    {
        if (is_array($this->value)) {
            $value = $this->value;
            $start = array_shift($value);
            $end = array_pop($value);

            if (!self::isEmpty($start) && !self::isEmpty($end)) {
                return [self::getOperator(), $this->column, $start, $end];
            }

            if (!self::isEmpty($start)) {
                return [GreaterThanOrEqual::getOperator(), $this->column, $start];
            }

            if (!self::isEmpty($end)) {
                return [LessThanOrEqual::getOperator(), $this->column, $end];
            }

            return [];
        }

        return parent::toArray();
    }
}
