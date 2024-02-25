<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

final class Exists implements FilterInterface
{
    public function __construct(
        private readonly QueryInterface $query,
    ) {
    }

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }
}
