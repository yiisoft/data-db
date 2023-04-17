<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use DateTimeInterface;
use Yiisoft\Data\Db\ColumnFormatterTrait;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

abstract class CompareFilter implements FilterInterface
{
    use ColumnFormatterTrait;

    public static string $mainDateTimeFormat = 'Y-m-d H:i:s';

    protected mixed $value;

    protected bool $ignoreNull = false;
    protected ?string $dateTimeFormat = null;

    /**
     * @param string|ExpressionInterface $column
     * @param mixed $value
     * @param string|null $table
     */
    public function __construct(string|ExpressionInterface $column, mixed $value, ?string $table = null)
    {
        $this->value = $value;
        $this->setColumn($column, $table);
    }

    public function withIgnoreNull(bool $ignoreNull = true): static
    {
        $new = clone $this;
        $new->ignoreNull = $ignoreNull;

        return $new;
    }

    public function withDateTimeFormat(?string $format): static
    {
        $new = clone $this;
        $new->dateTimeFormat = $format;

        return $new;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function formatValue(mixed $value): mixed
    {
        $format = $this->dateTimeFormat ?? static::$mainDateTimeFormat;

        if ($format && $value instanceof DateTimeInterface) {
            return $value->format($format);
        }

        return $value;
    }

    /**
     * @param array $values
     * @psalm-param array<int, mixed> $values
     *
     * @return array
     */
    protected function formatValues(array $values): array
    {
        return array_map([$this, 'formatValue'], $values);
    }

    public function toCriteriaArray(): array
    {
        if ($this->value === null) {
            return $this->ignoreNull ? [] : (new IsNull($this->column))->toCriteriaArray();
        }

        if (is_array($this->value)) {
            $value = $this->formatValues($this->value);
        } else {
            $value = $this->formatValue($this->value);
        }

        return [static::getOperator(), $this->column , $value];
    }
}
