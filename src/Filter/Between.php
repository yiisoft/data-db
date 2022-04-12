<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use function count;

final class Between extends CompareFilter
{
    /**
     * @param mixed $column
     */
    public function __construct($column, ?array $value, ?string $table = null)
    {
        if (is_array($value) && count($value) !== 2) {
            throw new InvalidArgumentException('Value must be a [from, to] array.');
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
        return $value === null || $value === '';
    }

    public function toArray(): array
    {
        if (is_array($this->value)) {
            $value = $this->value;
            $start = array_shift($value);
            $end = array_pop($value);
            $isStartEmpty = self::isEmpty($start);
            $isEndEmpty = self::isEmpty($end);

            if (!$isStartEmpty && !$isEndEmpty) {
                return [
                    self::getOperator(),
                    $this->column,
                    $this->formatValue($start),
                    $this->formatValue($end),
                ];
            }

            if (!$isStartEmpty) {
                return (new GreaterThanOrEqual($this->column, $start))->toArray();
            }

            if (!$isEndEmpty) {
                return (new LessThanOrEqual($this->column, $end))->toArray();
            }

            return [];
        }

        return parent::toArray();
    }
}
