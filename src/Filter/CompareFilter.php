<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\FilterInterface;

abstract class CompareFilter implements FilterInterface
{
    protected string $column;

    /**
     * @var array|bool|float|int|string|null
     */
    protected $value;

    /**
    * @var bool
    */
    protected bool $ignoreNull = false;

    /**
     * @param mixed $value
     */
    public function __construct(string $column, $value, ?string $table = null)
    {
        $this->value = $value;

        if ($table) {
            $this->column = $table . '.' . $column;
        } else {
            $this->column = $column;
        }
    }

    public function withIgnoreNull(bool $ignoreNull = false): self
    {
        $new = clone $this;
        $new->ignoreNull = $ignoreNull;
        return $new;
    }

    public function getIgnoreNull(): bool
    {
        return $this->ignoreNull;
    }

    public function toArray(): array
    {
        if ($this->value === null)
        {
            if ($this->ignoreNull) {
                return [];
            }

            return ['IS', $this->column, null];
        }

        return [static::getOperator(), $this->column , $this->value];
    }
}
