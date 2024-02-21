<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\FilterInterface;

interface QueryFilterInterface extends FilterInterface
{
    public function getParams(): array;
}
