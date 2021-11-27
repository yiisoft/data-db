<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql\Filter;

use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Expression\ArrayExpression;
use Yiisoft\Data\Db\Filter\CompareFilter;

final class ArrayContains extends CompareFilter
{
    private ?string $type = null;
    private int $dimension = 1;

    public static function getOperator(): string
    {
        return 'pgsql.array_contains';
    }

    public function withType(?string $type): self
    {
        $new = clone $this;
        $new->type = $type;

        return $new;
    }

    public function withDimension(int $dimension): self
    {
        $new = clone $this;
        $new->dimension = $dimension;

        return $new;
    }

    public function toArray(): array
    {
        if ($this->value === null) {
            return parent::toArray();
        }

        if ($this->value instanceof ExpressionInterface) {
            $value = $this->value;
        } else {
            $value = new ArrayExpression((array) $this->value, $this->type, $this->dimension);
        }

        return ['&&', $this->column, $value];
    }
}
