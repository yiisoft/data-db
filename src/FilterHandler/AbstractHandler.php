<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Db\Filter\QueryFilterInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\QueryInterface;
use Yiisoft\Db\QueryBuilder\Condition\AndCondition;
use Yiisoft\Db\QueryBuilder\Condition\BetweenCondition;
use Yiisoft\Db\QueryBuilder\Condition\ExistsCondition;
use Yiisoft\Db\QueryBuilder\Condition\InCondition;
use Yiisoft\Db\QueryBuilder\Condition\LikeCondition;
use Yiisoft\Db\QueryBuilder\Condition\NotCondition;
use Yiisoft\Db\QueryBuilder\Condition\OrCondition;
use Yiisoft\Db\QueryBuilder\Condition\SimpleCondition;

use function array_is_list;
use function array_shift;
use function count;
use function get_debug_type;
use function is_string;
use function sprintf;
use function strtoupper;

abstract class AbstractHandler implements QueryHandlerInterface
{
    /**
     * @param array $criteria
     * @return array{0: string, 1: array}
     */
    protected function splitCriteria(array $criteria): array
    {
        if (!array_is_list($criteria) || count($criteria) < 2) {
            throw new LogicException(
                sprintf('Incorrect criteria for the "%s" operator.', $this->getOperator())
            );
        }

        $operator = array_shift($criteria);

        if (!is_string($operator)) {
            throw new InvalidArgumentException(
                sprintf('$operator must be type of "string". "%s" given.', get_debug_type($operator))
            );
        }

        return [strtoupper($operator), $criteria];
    }

    /**
     * @param array $criteria
     * @param CriteriaHandler $criteriaHandler
     * @return array|ExpressionInterface|null
     * @throws \Yiisoft\Db\Exception\InvalidArgumentException
     */
    public function getCondition(array $criteria, CriteriaHandler $criteriaHandler): array|ExpressionInterface|null
    {
        if ($criteria === []) {
            return null;
        }

        [$operator, $criteria] = $this->splitCriteria($criteria);

        return match($operator) {
            'OR' => new OrCondition($criteria),
            'AND' => new AndCondition($criteria),
            'IN', 'NOT IN' => InCondition::fromArrayDefinition($operator, $criteria),
            'NOT' => NotCondition::fromArrayDefinition($operator, $criteria),
            'EXISTS', 'NOT EXISTS' => ExistsCondition::fromArrayDefinition($operator, $criteria),
            'BETWEEN', 'NOT BETWEEN' => BetweenCondition::fromArrayDefinition($operator, $criteria),
            'LIKE', 'ILIKE', 'OR LIKE', 'OR ILIKE', 'NOT LIKE', 'NOT ILIKE' => LikeCondition::fromArrayDefinition($operator, $criteria),
            default => SimpleCondition::fromArrayDefinition($operator, $criteria),
        };
    }

    /**
     * @param QueryInterface $query
     * @param FilterInterface $filter
     * @param CriteriaHandler $criteriaHandler
     * @return QueryInterface
     * @throws \Yiisoft\Db\Exception\InvalidArgumentException
     */
    public function applyFilter(QueryInterface $query, FilterInterface $filter, CriteriaHandler $criteriaHandler): QueryInterface
    {
        $criteria = $filter->toCriteriaArray();
        $params = $filter instanceof QueryFilterInterface ? $filter->getParams() : [];

        if ($criteria && $condition = $this->getCondition($criteria, $criteriaHandler)) {
            $query->andWhere($condition, $params);
        }

        return $query;
    }

    /**
     * @param QueryInterface $query
     * @param FilterInterface $filter
     * @param CriteriaHandler $criteriaHandler
     * @return QueryInterface
     * @throws \Yiisoft\Db\Exception\InvalidArgumentException
     *
     */
    public function applyHaving(QueryInterface $query, FilterInterface $filter, CriteriaHandler $criteriaHandler): QueryInterface
    {
        $criteria = $filter->toCriteriaArray();
        $params = $filter instanceof QueryFilterInterface ? $filter->getParams() : [];

        if ($criteria && $condition = $this->getCondition($criteria, $criteriaHandler)) {
            $query->andHaving($condition, $params);
        }

        return $query;
    }
}
