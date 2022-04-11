<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Db\DateTimeTrait;

final class Between extends CompareFilter
{
    use DateTimeTrait;

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
            $start = $this->dateTimeFormat(array_shift($value));
            $end = $this->dateTimeFormat(array_pop($value));
            $isStartEmpty = self::isEmpty($start);
            $isEndEmpty = self::isEmpty($end);

            if (!$isStartEmpty && !$isEndEmpty) {
                return [self::getOperator(), $this->column, $start, $end];
            }

            if (!$isStartEmpty) {
                return [GreaterThanOrEqual::getOperator(), $this->column, $start];
            }

            if (!$isEndEmpty) {
                return [LessThanOrEqual::getOperator(), $this->column, $end];
            }

            return [];
        }

        return parent::toArray();
    }
}
