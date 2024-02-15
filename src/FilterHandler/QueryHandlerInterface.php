<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

interface QueryHandlerInterface extends FilterHandlerInterface
{
    public function getCondition(FilterInterface $filter): ?array;
}
