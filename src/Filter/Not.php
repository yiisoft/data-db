<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Not as FilterNot;
use Yiisoft\Data\Reader\FilterInterface;

final class Not implements FilterInterface
{
    private FilterInterface $filter;
    private array $operators = [];

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
        $this->operators = [
            IsNull::getOperator() => 'IS NOT',
            In::getOperator() => 'NOT IN',
            Exists::getOperator() => 'NOT EXISTS',
            Between::getOperator() => 'NOT BETWEEN',
            GreaterThan::getOperator() => '<=',
            GreaterThanOrEqual::getOperator() => '<',
            LessThan::getOperator() => '>=',
            LessThanOrEqual::getOperator() => '>',
            Like::getOperator() => 'NOT LIKE',
            ILike::getOperator() => 'NOT ILIKE',
            Equals::getOperator() => '!='
        ];
    }

    public static function getOperator(): string
    {
        return FilterNot::getOperator();
    }

    public function withOperator(string $operator, ?string $inverse): self
    {
        $new = clone $this;

        if ($inverse === null) {
            unset($new->operators[$operator]);
        } else {
            $new->operators[$operator] = $inverse;
        }

        return $new;
    }

    public function withoutOperator(string $operator): self
    {
        return $this->withOperator($operator, null);
    }

    public function toCriteriaArray(): array
    {
        $array = $this->filter->toCriteriaArray();

        if ($array === []) {
            return [];
        }

        $operator = $array[0];

        if (isset($this->operators[$operator])) {
            $array[0] = $this->operators[$operator];
        } else {
            $array = [self::getOperator(), $array];
        }

        return $array;
    }
}
