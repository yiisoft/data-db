<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use RuntimeException;
use Stringable;

final class NotEquals extends CompareFilter
{
    public static function getOperator(): string
    {
        return '!=';
    }

    public function toArray(): array
    {
        if ($this->value === null) {
            if ($this->ignoreNull) {
                return [];
            }

            if (is_string($this->column)) {
                return ['NOT', [$this->column => null]];
            }

            if ($this->column instanceof Stringable) {
                return ['NOT', [$this->column->__toString() => null]];
            }

            throw new RuntimeException();
        }

        return [self::getOperator(), $this->column, $this->value];
    }
}
