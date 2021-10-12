<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Db\Query\Query;
use Yiisoft\Data\Reader\Filter\Equals as FilterEquals;

final class Equals extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterEquals::getOperator();
    }

    public function toArray(): array
    {
        if (is_array($this->value) || $this->value instanceof Query) {
            return [In::getOperator(), $this->column, $this->value];
        }

        return parent::toArray();
    }
}
