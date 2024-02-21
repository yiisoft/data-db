<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\QueryInterface;

interface QueryHandlerInterface extends FilterHandlerInterface
{
    public function getCondition(array $criteria, CriteriaHandler $criteriaHandler): array|ExpressionInterface|null;

    public function applyFilter(QueryInterface $query, FilterInterface $filter, CriteriaHandler $criteriaHandler): QueryInterface;

    public function applyHaving(QueryInterface $query, FilterInterface $filter, CriteriaHandler $criteriaHandler): QueryInterface;
}
