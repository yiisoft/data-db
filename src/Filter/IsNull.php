<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Db\ColumnFormatterTrait;
use Yiisoft\Data\Reader\Filter\FilterInterface;

final class IsNull implements FilterInterface
{
    use ColumnFormatterTrait;

    /**
     * @param mixed $column
     * @param string|null $table
     */
    public function __construct($column, ?string $table = null)
    {
        $this->setColumn($column, $table);
    }

    public static function getOperator(): string
    {
        return 'is';
    }

    public function toArray(): array
    {
        return [self::getOperator(), $this->column, null];
    }
}
