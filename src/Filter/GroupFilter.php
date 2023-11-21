<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Data\Reader\FilterInterface;

use function array_shift;
use function count;
use function get_debug_type;
use function is_array;
use function sprintf;

/**
 *  @psalm-consistent-constructor
 */
abstract class GroupFilter implements FilterInterface
{
    private array $filters;

    public function __construct(FilterInterface ...$filters)
    {
        $this->filters = $filters;
    }

    public function toCriteriaArray(): array
    {
        $array = [static::getOperator()];

        foreach ($this->filters as $filter) {
            if ($filter instanceof FilterInterface) {
                $arr = $filter->toCriteriaArray();
            } elseif (is_array($filter)) {
                $arr = $filter;
            } else {
                throw new RuntimeException(
                    sprintf(
                        '$filter must be instance of "%s" or array. %s given.',
                        FilterInterface::class,
                        get_debug_type($filter)
                    )
                );
            }

            if ($arr !== []) {
                $array[] = $arr;
            }
        }

        return count($array) > 1 ? $array : [];
    }

    public function withCriteriaArray(array $criteriaArray): static
    {
        return static::fromCriteriaArray($criteriaArray);
    }

    public static function fromCriteriaArray(array $criteriaArray): static
    {
        foreach ($criteriaArray as $key => $criteria) {
            if (!is_array($criteria)) {
                throw new InvalidArgumentException(sprintf('Invalid filter on "%s" key.', $key));
            }

            $operator = array_shift($criteria);

            if (!is_string($operator) || $operator === '') {
                throw new InvalidArgumentException(sprintf('Invalid filter operator on "%s" key.', $key));
            }
        }

        $filter = new static();
        $filter->filters = $criteriaArray;

        return $filter;
    }
}
