<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\EqualsNull;

final class EqualsNullHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return EqualsNull::getOperator();
    }

    protected function splitCriteria(array $criteria): array
    {
        [, $criteria] = parent::splitCriteria($criteria);

        return ['IS', [$criteria[0], null]];
    }
}
