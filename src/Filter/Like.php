<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Like as FilterLike;

class Like extends CompareFilter
{
    private bool $start = true;
    private bool $end = true;

    /**
    * @param mixed $column
    */
    public function __construct($column, ?string $value, ?string $table = null)
    {
        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return FilterLike::getOperator();
    }

    public function withBoth(): self
    {
        return $this
            ->withStart()
            ->withEnd();
    }

    public function withoutBoth(): self
    {
        return $this
            ->withoutStart()
            ->withoutEnd();
    }

    public function withStart(): self
    {
        $new = clone $this;
        $new->start = true;

        return $new;
    }

    public function withoutStart(): self
    {
        $new = clone $this;
        $new->start = false;

        return $new;
    }

    public function withEnd(): self
    {
        $new = clone $this;
        $new->end = true;

        return $new;
    }

    public function withoutEnd(): self
    {
        $new = clone $this;
        $new->end = false;

        return $new;
    }

    public function toArray(): array
    {
        if ($this->value === null || ($this->start && $this->end)) {
            return parent::toArray();
        }

        $value = $this->formatValue($this->value);

        if (!$this->start && !$this->end) {
            return [static::getOperator(), $this->column, $value, false];
        }

        $value = $this->start ? '%' . $value : $value . '%';

        return [static::getOperator(), $this->column, $value, false];
    }
}
