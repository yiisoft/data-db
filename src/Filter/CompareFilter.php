<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use DateTimeInterface;
use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

abstract class CompareFilter implements FilterInterface
{
    public static string $mainDateTimeFormat = 'Y-m-d H:i:s';

    /**
     * @var ExpressionInterface|string
     */
    protected $column;

    /**
     * @var array|bool|float|int|string|null
     */
    protected $value;

    protected bool $ignoreNull = false;
    protected ?string $dateTimeFormat = null;

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
            throw new InvalidArgumentException('Column must be string or instance of "' . ExpressionInterface::class . '". "' . $type . '" given.');
        }
    }

    public function withIgnoreNull(bool $ignoreNull = true): self
    {
        $new = clone $this;
        $new->ignoreNull = $ignoreNull;

        return $new;
    }

    public function withDateTimeFormat(?string $format): self
    {
        $new = clone $this;
        $new->dateTimeFormat = $format;

        return $new;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function formatValue($value)
    {
        $format = $this->dateTimeFormat ?? self::$mainDateTimeFormat;

        if ($format && $value instanceof DateTimeInterface) {
            return $value->format($format);
        }

        return $value;
    }

    /**
     * @psalm-param array<int, mixed> $values
     *
     * @return array
     */
    protected function formatValueMultiple(array $values): array
    {
        return array_map([$this, 'formatValue'], $values);
    }

    public function toArray(): array
    {
        if ($this->value === null) {
            return $this->ignoreNull ? [] : ['IS', $this->column, null];
        }

        if (is_array($this->value)) {
            $value = $this->formatValueMultiple($this->value);
        } else {
            $value = $this->formatValue($this->value);
        }

        return [static::getOperator(), $this->column , $value];
    }
}
