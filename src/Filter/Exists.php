<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryInterface;

final class Exists implements FilterInterface
{
    public function __construct(private QueryInterface $query)
    {
    }

    public static function getOperator(): string
    {
        return 'exists';
    }

    public function toCriteriaArray(): array
    {
        return [self::getOperator(), $this->query];
    }
}
