<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Yiisoft\Db\Expression\ExpressionInterface;

use function is_string;

trait ColumnFormatterTrait
{
    protected string|ExpressionInterface $column;

    /**
     * @param ExpressionInterface|string $column
     * @param string|null $table
     */
    private function setColumn(string|ExpressionInterface $column, ?string $table = null): void
    {
        if (is_string($column)) {
            if ($table) {
                $this->column = $table . '.' . $column;
            } else {
                $this->column = $column;
            }
        } else {
            $this->column = $column;
        }
    }
}
