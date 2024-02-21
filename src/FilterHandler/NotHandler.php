<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Reader\Filter\Not;
use Yiisoft\Db\Expression\ExpressionInterface;

use function array_is_list;
use function get_debug_type;
use function sprintf;

final class NotHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return Not::getOperator();
    }

    /**
     * @param array $criteria
     * @param CriteriaHandler $criteriaHandler
     * @return array|ExpressionInterface|null
     * @throws \Yiisoft\Db\Exception\InvalidArgumentException
     *
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArgument
     * @psalm-suppress InvalidOperand
     */
    public function getCondition(array $criteria, CriteriaHandler $criteriaHandler): array|ExpressionInterface|null
    {
        if (!array_is_list($criteria)) {
            throw new LogicException(
                sprintf('Incorrect criteria for the "%s" operator.', $this->getOperator())
            );
        }

        if (!isset($criteria[1])) {
            throw new LogicException('"Not" criteria must be set.');
        }

        if (!is_array($criteria[1]) || !array_is_list($criteria[1]) || $criteria[1] === []) {
            throw new LogicException(
                sprintf('"Not" criteria must be a non zero list. "%s" given.', get_debug_type($criteria[1]))
            );
        }

        [$operator, $subCriteria] = $this->splitCriteria($criteria[1]);
        $subOperator = match($operator) {
            'IS' => 'IS NOT',
            'IN' => 'NOT IN',
            'EXISTS' => 'NOT EXISTS',
            'BETWEEN' => 'NOT BETWEEN',
            'LIKE' => 'NOT LIKE',
            'ILIKE' => 'NOT ILIKE',
            '>' => '<=',
            '>=' => '<',
            '<' => '>=',
            '<=' => '>',
            '=' => '!=',
            default => $operator,
        };

        if ($operator !== $subOperator) {
            return parent::getCondition([$subOperator, ...$subCriteria], $criteriaHandler);
        }

        return parent::getCondition($criteria, $criteriaHandler);
    }
}
