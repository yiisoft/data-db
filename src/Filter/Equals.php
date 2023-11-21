<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Equals as FilterEquals;
use Yiisoft\Db\Query\QueryInterface;

final class Equals extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterEquals::getOperator();
    }

    public function toCriteriaArray(): array
    {
        if (is_array($this->value) || $this->value instanceof QueryInterface) {
            return (new In($this->column, $this->value))->toCriteriaArray();
        }

        return parent::toCriteriaArray();
    }
}
