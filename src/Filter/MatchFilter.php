<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Db\Expression\ExpressionInterface;

use function is_scalar;

abstract class MatchFilter extends CompareFilter
{
    private bool $start = true;
    private bool $end = true;

    /**
     * @param ExpressionInterface|string $column
     * @param array|ExpressionInterface|float|int|string|null $value
     * @param array $params
     */
    public function __construct(
        string|ExpressionInterface $column,
        string|int|float|ExpressionInterface|array|null $value,
        array $params = []
    ) {
        parent::__construct($column, $value, $params);
    }

    public function withBoth(): static
    {
        return $this->withStart()->withEnd();
    }

    public function withoutBoth(): static
    {
        return $this->withoutStart()->withoutEnd();
    }

    public function withStart(): static
    {
        if ($this->start === true) {
            return $this;
        }

        $new = clone $this;
        $new->start = true;

        return $new;
    }

    public function withoutStart(): static
    {
        if ($this->start === false) {
            return $this;
        }

        $new = clone $this;
        $new->start = false;

        return $new;
    }

    public function withEnd(): static
    {
        if ($this->end === true) {
            return $this;
        }

        $new = clone $this;
        $new->end = true;

        return $new;
    }

    public function withoutEnd(): static
    {
        if ($this->end === false) {
            return $this;
        }

        $new = clone $this;
        $new->end = false;

        return $new;
    }

    public function toCriteriaArray(): array
    {
        if (!is_scalar($this->value) || ($this->start && $this->end)) {
            return parent::toCriteriaArray();
        }

        if (!$this->start && !$this->end) {
            return [static::getOperator(), $this->column, $this->value, null];
        }

        $value = $this->start ? '%' . $this->value : $this->value . '%';

        return [static::getOperator(), $this->column, $value, null];
    }
}
