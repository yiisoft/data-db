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

    public function withStart(bool $start = true): self
    {
        $new = clone $this;
        $new->start = $start;

        return $new;
    }

    public function withEnd(bool $end = true): self
    {
        $new = clone $this;
        $new->end = $end;

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

        $value = $this->start ? $this->value . '%' : '%' . $this->value;

        return [self::getOperator(), $this->column, $value, false];
    }
}
