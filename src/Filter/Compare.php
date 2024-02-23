<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use DateTimeInterface;
use Yiisoft\Data\Reader\FilterInterface;

abstract class Compare implements FilterInterface
{
    use Params;

    /**
     * @param string $field Name of the field to compare.
     * @param bool|DateTimeInterface|float|int|string $value Value to compare to.
     */
    public function __construct(
        private string $field,
        private bool|DateTimeInterface|float|int|string $value
    ) {
    }

    /**
     * @param bool|DateTimeInterface|float|int|string $value Value to compare to.
     */
    final public function withValue(bool|DateTimeInterface|float|int|string $value): static
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }

    public function toCriteriaArray(): array
    {
        return [static::getOperator(), $this->field, $this->value, $this];
    }
}
