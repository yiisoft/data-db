<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use DateTimeInterface;
use InvalidArgumentException;
use Yiisoft\Db\Expression\ExpressionInterface;

use function is_array;

abstract class MatchFilter extends CompareFilter
{
    private bool $start = true;
    private bool $end = true;

    /**
     * @param ExpressionInterface|string $column
     * @param array|DateTimeInterface|ExpressionInterface|float|int|string|null $value
     * @param string|null $table
     */
    public function __construct(
        string|ExpressionInterface $column,
        string|int|float|DateTimeInterface|ExpressionInterface|array|null $value,
        ?string $table = null
    ) {
        parent::__construct($column, $value, $table);
    }

    protected function formatValue(mixed $value): string|ExpressionInterface|null
    {
        if (is_array($value)) {
            throw new InvalidArgumentException("arrays can't be using as value");
        }

        $value = parent::formatValue($value);

        if ($value !== null && !$value instanceof ExpressionInterface) {
            return (string) $value;
        }

        return $value;
    }

    public function withBoth(): static
    {
        return $this
            ->withStart()
            ->withEnd();
    }

    public function withoutBoth(): static
    {
        return $this
            ->withoutStart()
            ->withoutEnd();
    }

    public function withStart(): static
    {
        $new = clone $this;
        $new->start = true;

        return $new;
    }

    public function withoutStart(): static
    {
        $new = clone $this;
        $new->start = false;

        return $new;
    }

    public function withEnd(): static
    {
        $new = clone $this;
        $new->end = true;

        return $new;
    }

    public function withoutEnd(): static
    {
        $new = clone $this;
        $new->end = false;

        return $new;
    }

    public function toCriteriaArray(): array
    {
        $value = is_array($this->value) ? $this->formatValues($this->value) : $this->formatValue($this->value);

        if (!is_string($value) || ($this->start && $this->end)) {
            return parent::toCriteriaArray();
        }

        if (!$this->start && !$this->end) {
            return [static::getOperator(), $this->column, $value, null];
        }

        $value = $this->start ? '%' . $value : $value . '%';

        return [static::getOperator(), $this->column, $value, null];
    }
}
