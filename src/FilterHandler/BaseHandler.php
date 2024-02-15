<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\CriteriaHandler;

abstract class BaseHandler implements QueryHandlerInterface
{
    public function getCondition(string $operator, array $operands, CriteriaHandler $criteriaHandler): ?array
    {
        return ConditionFactory::make($operator, $operands, $criteriaHandler);
    }
}
