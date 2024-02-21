<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\EqualsEmpty;
use Yiisoft\Data\Reader\Filter\EqualsEmpty as BaseEqualsEmptyFilter;

use function strcasecmp;

final class EqualsEmptyHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return EqualsEmpty::getOperator();
    }

    protected function splitCriteria(array $criteria): array
    {
        [$operator, $criteria] = parent::splitCriteria($criteria);

        if (strcasecmp($operator, BaseEqualsEmptyFilter::getOperator()) === 0) {
            return ['IS', [$criteria[0], null]];
        }

        return [$operator, $criteria];
    }
}
