<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Equals as FilterEquals;

class Equals extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterEquals::getOperator();
    }

    public function toArray(): array
    {
        if ($this->value === null && $this->ignoreNull) {
            return [];
        }

        return parent::toArray();
    }
}
