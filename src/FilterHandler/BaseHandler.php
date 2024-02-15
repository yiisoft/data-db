<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;

abstract class BaseHandler implements QueryHandlerInterface
{
    public function getCondition(string $operator, array $operands): ?array
    {
        return ConditionFactory::make($operator, $operands);
    }
}
