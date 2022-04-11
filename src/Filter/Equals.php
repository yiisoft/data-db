<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Db\Query\QueryInterface;
use Yiisoft\Data\Reader\Filter\Equals as FilterEquals;

final class Equals extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterEquals::getOperator();
    }

    public function toArray(): array
    {
        if (is_array($this->value) || $this->value instanceof QueryInterface) {
            return (new In($this->column, $this->value))->toArray();
        }

        return parent::toArray();
    }
}
