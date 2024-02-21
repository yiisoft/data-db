<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Between as BetweenFilter;
use Yiisoft\Db\Expression\ExpressionInterface;

final class Between implements QueryFilterInterface
{
    use ParamsTrait;

    public function __construct(
        private readonly string|ExpressionInterface $column,
        private readonly mixed $min,
        private readonly mixed $max,
        array $params = []
    ) {
        $this->params = $params;
    }

    public static function getOperator(): string
    {
        return BetweenFilter::getOperator();
    }

    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    public function toCriteriaArray(): array
    {
        $isMinEmpty = self::isEmpty($this->min);
        $isMaxEmpty = self::isEmpty($this->max);

        if (!$isMinEmpty && !$isMaxEmpty) {
            return [
                self::getOperator(),
                $this->column,
                $this->min,
                $this->max,
            ];
        }

        if (!$isMinEmpty) {
            return (new GreaterThanOrEqual($this->column, $this->min))->toCriteriaArray();
        }

        if (!$isMaxEmpty) {
            return (new LessThanOrEqual($this->column, $this->max))->toCriteriaArray();
        }

        return [];
    }
}
