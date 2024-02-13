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

    protected bool $ignoreNull = false;
    protected ?string $dateTimeFormat = null;

    public function __construct(string|ExpressionInterface $column, protected mixed $value, ?string $table = null)
    {
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

    protected function formatValue(mixed $value): mixed
    {
        $format = $this->dateTimeFormat ?? static::$mainDateTimeFormat;

        if ($format && $value instanceof DateTimeInterface) {
            return $value->format($format);
        }

        return $value;
    }

    protected function formatValues(array $values): array
    {
        return array_map($this->formatValue(...), $values);
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
