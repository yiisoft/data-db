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
            throw new InvalidArgumentException('Value must be array with 2 elements');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return 'between';
    }

    public function toArray(): array
    {
        if (is_array($this->value)) {
            $value = $this->value;
            $start = array_shift($value);
            $end = array_pop($value);

            return [self::getOperator(), $this->column, $start, $end];
        }


        return parent::toArray();
    }
}
