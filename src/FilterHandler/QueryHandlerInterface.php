<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterHandlerInterface;

interface QueryHandlerInterface extends FilterHandlerInterface
{
    public function getCondition(array $operands, Context $context): ?array;
}
