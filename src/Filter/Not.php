<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Not as FilterNot;
use Yiisoft\Data\Reader\FilterInterface;

use function array_key_first;
use function strtoupper;

final class Not implements FilterInterface
{
    private array $operators = [
        'IS' => 'IS NOT',
        'IN' => 'NOT IN',
        'EXISTS' => 'NOT EXISTS',
        'BETWEEN' => 'NOT BETWEEN',
        'LIKE' => 'NOT LIKE',
        'ILIKE' => 'NOT ILIKE',
        '>' => '<=',
        '>=' => '<',
        '<' => '>=',
        '<=' => '>',
        '=' => '!=',
    ];

    public function __construct(private FilterInterface $filter, ?array $operators = null)
    {
        if ($operators !== null) {
            $this->operators = $operators;
        }
    }

    public static function getOperator(): string
    {
        return FilterNot::getOperator();
    }

    public function withOperator(string $operator, ?string $inverse): self
    {
        $new = clone $this;
        $operator = strtoupper($operator);

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

        $key = array_key_first($array);
        $operator = is_string($array[$key]) ? strtoupper($array[$key]) : null;

        if ($operator !== null && isset($this->operators[$operator])) {
            $array[0] = $this->operators[$operator];
        } else {
            $array = [self::getOperator(), $array];
        }

        return $array;
    }
}
