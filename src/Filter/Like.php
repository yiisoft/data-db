<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Like as FilterLike;

class Like extends CompareFilter
{
    private bool $start = true;
    private bool $end = true;

    public function __construct(string $column, ?string $value, ?string $table = null)
    {
        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return FilterLike::getOperator();
    }

    public function withStart(): self
    {
        if ($this->start === true) {
            return $this;
        }

        $new = clone $this;
        $new->start = true;

        return $new;
    }

    public function withoutStart(): self
    {
        if ($this->start === false) {
            return $this;
        }

        $new = clone $this;
        $new->start = false;

        return $new;
    }

    public function withEnd(): self
    {
        if ($this->end === true) {
            return $this;
        }

        $new = clone $this;
        $new->end = true;

        return $new;
    }

    public function withoutEnd(): self
    {
        if ($this->end === false) {
            return $this;
        }

        $new = clone $this;
        $new->end = false;

        return $new;
    }

    public function toArray(): array
    {
        if ($this->value === null || ($this->start && $this->end)) {
            return parent::toArray();
        }

        if (!$this->start && !$this->end) {
            return [self::getOperator(), $this->column, $this->value, false];
        }

        $value = $this->start ? '%' . $this->value : $this->value . '%';

        return [self::getOperator(), $this->column, $value, false];
    }
}
