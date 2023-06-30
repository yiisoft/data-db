<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\EqualsEmpty;

final class EqualsEmptyHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function getOperator(): string
    {
        return EqualsEmpty::getOperator();
    }
}
