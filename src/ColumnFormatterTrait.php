<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use InvalidArgumentException;
use Yiisoft\Db\Expression\ExpressionInterface;

trait ColumnFormatterTrait
{
    /**
     * @var ExpressionInterface|string
     */
    protected $column;

    /**
     * @param mixed $column
     * @param string|null $table
     *
     * @throws InvalidArgumentException
     */
    private function setColumn($column, ?string $table = null): void
    {
        if (\is_string($column)) {
            if ($table) {
                $this->column = $table . '.' . $column;
            } else {
                $this->column = $column;
            }
        } elseif ($column instanceof ExpressionInterface) {
            $this->column = $column;
        } else {
            $type = \is_object($column) ? \get_class($column) : \gettype($column);

            throw new InvalidArgumentException('Column must be string or instance of "' . ExpressionInterface::class . '". "' . $type . '" given.');
        }
    }
}
