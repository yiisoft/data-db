<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Db\Expression\ExpressionInterface;

abstract class CompareFilter implements QueryFilterInterface
{
    use ParamsTrait;

    protected bool $ignoreNull = false;

    /**
     * @param ExpressionInterface|string $column
     * @param mixed $value
     * @param array $params
     */
    public function __construct(
        protected readonly string|ExpressionInterface $column,
        protected mixed $value,
        array $params = []
    ) {
        $this->params = $params;
    }

    public function withIgnoreNull(bool $ignoreNull = true): static
    {
        if ($this->ignoreNull === $ignoreNull) {
            return $this;
        }

        $new = clone $this;
        $new->ignoreNull = $ignoreNull;

        return $new;
    }

    public function toCriteriaArray(): array
    {
        if ($this->value === null) {
            return $this->ignoreNull ? [] : (new EqualsNull($this->column))->toCriteriaArray();
        }

        return [static::getOperator(), $this->column , $this->value];
    }
}
