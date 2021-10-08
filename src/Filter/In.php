<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\In as FilterIn;
use Yiisoft\Db\Query\Query;
use function is_array;

final class In extends CompareFilter
{
    /**
     * @param mixed $value
     */
    public function __construct(string $column, $value, ?string $table = null)
    {
        if ($value !== null && !is_array($value) && $value instanceof Query === false) {
            throw new InvalidArgumentException('value must be null, array or ' . Query::class . ' instance');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return FilterIn::getOperator();
    }
}
