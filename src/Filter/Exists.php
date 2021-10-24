<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Db\Query\Query;
use Yiisoft\Data\Reader\Filter\FilterInterface;

final class Exists implements FilterInterface
{
    private Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public static function getOperator(): string
    {
        return 'exists';
    }

    public function toArray(): array
    {
        return [self::getOperator(), $this->query];
    }
}
