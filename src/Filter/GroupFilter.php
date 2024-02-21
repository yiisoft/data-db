<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Reader\FilterInterface;

use function array_merge;
use function array_shift;
use function count;
use function is_array;
use function sprintf;

/**
 *  @psalm-consistent-constructor
 */
abstract class GroupFilter implements QueryFilterInterface
{
    /**
     * @var array[]|FilterInterface[]
     */
    private array $filters;

    public function __construct(FilterInterface ...$filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function toCriteriaArray(): array
    {
        $array = [static::getOperator()];

        foreach ($this->filters as $filter) {
            $arr = $filter instanceof FilterInterface ? $filter->toCriteriaArray() : $filter;

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

    public function getParams(): array
    {
        $params = [];

        foreach ($this->filters as $filter) {
            if ($filter instanceof QueryFilterInterface && $array = $filter->getParams()) {
                $params[] = $array;
            }
        }

        if (isset($params[1])) {
            return array_merge(...$params);
        }

        return $params[0] ?? [];
    }

    /**
     * @param array $criteriaArray
     * @return static
     *
     * @psalm-suppress MixedPropertyTypeCoercion
     */
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
