<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

abstract class CompareFilter implements FilterInterface
{
    /**
     * @var ExpressionInterface|string
     */
    protected $column;

    /**
     * @var array|bool|float|int|string|null
     */
    protected $value;

    /**
    * @var bool
    */
    protected bool $ignoreNull = false;

    /**
     * @param mixed $column
     * @param mixed $value
     */
    public function __construct($column, $value, ?string $table = null)
    {
        $this->value = $value;

        if ($column instanceof ExpressionInterface) {
            $this->column = $column;
        } elseif (is_string($column)) {
            if ($table) {
                $this->column = $table . '.' . $column;
            } else {
                $this->column = $column;
            }
        } else {
            $type = \is_object($column) ? \get_class($column) : \gettype($column);
            throw new InvalidArgumentException('Column must be string or instance of "' . ExpressionInterface::class . '". "' . $type  .' given"');
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
        if ($this->value === null) {
            if ($this->ignoreNull) {
                return [];
            }

            return ['IS', $this->column, null];
        }

        return [static::getOperator(), $this->column , $this->value];
    }
}
