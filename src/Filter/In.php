<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\In as FilterIn;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\QueryInterface;

final class In extends CompareFilter
{
    /**
     * @param ExpressionInterface|string $column
     * @param array|QueryInterface|null $value
     * @param string|null $table
     */
    public function __construct(string|ExpressionInterface $column, array|QueryInterface|null $value, ?string $table = null)
    {
        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return FilterIn::getOperator();
    }
}
