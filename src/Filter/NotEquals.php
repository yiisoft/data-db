<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

final class NotEquals extends CompareFilter
{
    public static function getOperator(): string
    {
        return '!=';
    }

    public function toCriteriaArray(): array
    {
        if ($this->value === null) {
            if ($this->ignoreNull) {
                return [];
            }

            $isNull = new IsNull($this->column);

            return (new Not($isNull))->toCriteriaArray();
        }

        $value = $this->formatValue($this->value);

        return [self::getOperator(), $this->column, $value];
    }
}
