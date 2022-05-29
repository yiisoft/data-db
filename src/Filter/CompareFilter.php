<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use DateTimeInterface;
use Yiisoft\Data\Db\ColumnFormatterTrait;
use Yiisoft\Data\Reader\Filter\FilterInterface;

abstract class CompareFilter implements FilterInterface
{
    use ColumnFormatterTrait;

    public static string $mainDateTimeFormat = 'Y-m-d H:i:s';

    /**
     * @var mixed
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
        $this->setColumn($column, $table);
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
    protected function formatValues(array $values): array
    {
        return array_map([$this, 'formatValue'], $values);
    }

    public function toArray(): array
    {
        if ($this->value === null) {
            return $this->ignoreNull ? [] : (new IsNull($this->column))->toArray();
        }

        if (is_array($this->value)) {
            $value = $this->formatValues($this->value);
        } else {
            $value = $this->formatValue($this->value);
        }

        return [static::getOperator(), $this->column , $value];
    }
}
