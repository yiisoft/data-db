<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\In as FilterIn;
use Yiisoft\Db\Query\QueryInterface;

use function is_array;

final class In extends CompareFilter
{
    /**
     * @param mixed $column
     * @param mixed $value
     */
    public function __construct($column, $value, ?string $table = null)
    {
        if ($value !== null && !is_array($value) && !is_a($value, QueryInterface::class)) {
            throw new InvalidArgumentException('Value must be null, array or ' . QueryInterface::class . ' instance.');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return FilterIn::getOperator();
    }
}
