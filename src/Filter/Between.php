<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\Between as BetweenFilter;
use Yiisoft\Db\Expression\ExpressionInterface;

use function count;

final class Between extends CompareFilter
{
    public function __construct(string|ExpressionInterface $column, ?array $value, ?string $table = null)
    {
        if (is_array($value) && count($value) !== 2) {
            throw new InvalidArgumentException('Value must be a [from, to] array.');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return BetweenFilter::getOperator();
    }

    /**
     * @return bool
     */
    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    public function toCriteriaArray(): array
    {
        if (is_array($this->value)) {
            $value = $this->value;
            $start = $this->formatValue(array_shift($value));
            $end = $this->formatValue(array_pop($value));
            $isStartEmpty = self::isEmpty($start);
            $isEndEmpty = self::isEmpty($end);

            if (!$isStartEmpty && !$isEndEmpty) {
                return [
                    self::getOperator(),
                    $this->column,
                    $start,
                    $end,
                ];
            }

            if (!$isStartEmpty) {
                return (new GreaterThanOrEqual($this->column, $start))->toCriteriaArray();
            }

            if (!$isEndEmpty) {
                return (new LessThanOrEqual($this->column, $end))->toCriteriaArray();
            }

            return [];
        }

        return parent::toCriteriaArray();
    }
}
