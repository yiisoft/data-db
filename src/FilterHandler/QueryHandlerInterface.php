<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

interface QueryHandlerInterface extends FilterHandlerInterface
{
    public function applyFilter(QueryInterface $query, FilterInterface $filter): QueryInterface;

    public function applyHaving(QueryInterface $query, FilterInterface $having): QueryInterface;
}
