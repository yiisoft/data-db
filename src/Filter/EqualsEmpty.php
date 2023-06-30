<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Db\ColumnFormatterTrait;
use Yiisoft\Data\Reader\Filter\EqualsEmpty as FilterEqualsEmpty;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

use function array_unshift;
use function array_values;

final class EqualsEmpty implements FilterInterface
{
    use ColumnFormatterTrait;

    /**
     * @var FilterInterface[]
     */
    private array $filters = [];

    public function __construct(string|ExpressionInterface $column, ?string $table = null, FilterInterface ...$filters)
    {
        $this->setColumn($column, $table);

        if ($filters !== []) {
            $this->filters = $filters;
        } else {
            $this->filters = [
                new IsNull($this->column),
                new Equals($this->column, ''),
            ];
        }
    }

    public function withFilter(FilterInterface $filter): self
    {
        $new = clone $this;
        $new->filters[] = $filter;

        return $new;
    }

    public function withFilters(FilterInterface $filter, FilterInterface ...$filters): self
    {
        $new = clone $this;
        array_unshift($filters, $filter);

        $new->filters = $filters;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public static function getOperator(): string
    {
        return FilterEqualsEmpty::getOperator();
    }

    /**
     * @inheritDoc
     */
    public function toCriteriaArray(): array
    {
        $filters = array_values($this->filters);

        return (new Any(...$filters))->toCriteriaArray();
    }
}
