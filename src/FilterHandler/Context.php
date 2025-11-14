<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\FieldMapper\FieldMapperInterface;
use Yiisoft\Data\Db\FilterHandler;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

final class Context
{
    public function __construct(
        private readonly FilterHandler $filterHandler,
        private readonly FieldMapperInterface $fieldMapper,
    ) {}

    public function handleFilter(FilterInterface $filter): ConditionInterface
    {
        return $this->filterHandler->handle($filter);
    }

    public function mapField(string $field): string|ExpressionInterface
    {
        return $this->fieldMapper->map($field);
    }
}
