<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\EqualsEmpty as FilterEqualsEmpty;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;

use function in_array;
use function is_bool;
use function sprintf;

final class EqualsEmpty implements FilterInterface
{
    private array $values;

    public function __construct(
        private readonly string|ExpressionInterface $column,
        bool|string|int|float ...$values
    ) {
        $this->values = $this->prepareValues(...$values);
    }


    private function prepareValues(bool|string|int|float ...$values): array
    {
        $unique = [];

        foreach ($values as $value) {
            if (!empty($value)) {
                $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;

                throw new InvalidArgumentException(
                    sprintf('$value must be equal php "empty". "%s" given.', $value)
                );
            }

            if (!in_array($value, $unique, true)) {
                $unique[] = $value;
            }
        }

        return $unique;
    }

    /**
     * @inheritDoc
     */
    public static function getOperator(): string
    {
        return FilterEqualsEmpty::getOperator();
    }

    /**
     * @inheritDoc
     */
    public function toCriteriaArray(): array
    {
        $filters = [
            new EqualsNull($this->column),
        ];

        if ($this->values) {
            $filters[] = new In($this->column, $this->values);
        }

        return (new Any(...$filters))->toCriteriaArray();
    }
}
