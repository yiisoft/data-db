<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\QueryBuilder\Condition\AndCondition;
use Yiisoft\Db\QueryBuilder\Condition\OrCondition;

use function array_is_list;
use function count;
use function is_string;

/**
 * @internal
 */
abstract class GroupHandler extends AbstractHandler
{
    protected function normalizeCriteria(array $criteria, CriteriaHandler $criteriaHandler): array
    {
        [$operator, $subCriteria] = $this->splitCriteria($criteria);
        $normalized = [$operator];

        /** @psalm-var array<int, mixed> $value */
        foreach ($subCriteria as $value) {
            if (is_string($value[0]) && $tmp = $this->normalizeCondition($value, $criteriaHandler)) {
                $normalized[] = $tmp;
            } elseif (is_array($value[0])) {
                foreach ($value as $val) {
                    if (is_array($val) && $tmp = $this->normalizeCondition($val, $criteriaHandler)) {
                        $normalized[] = $tmp;
                    }
                }
            }
        }

        return $normalized;
    }

    private function normalizeCondition(array $value, CriteriaHandler $criteriaHandler): array|ExpressionInterface|null
    {
        if (!array_is_list($value) ||
            count($value) < 2 ||
            !is_string($value[0]) ||
            !$criteriaHandler->hasHandler($value[0])
        ) {
            return null;
        }

        return $criteriaHandler->getHandlerByOperator($value[0])
            ->getCondition($value, $criteriaHandler);
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

        [$operator, $criteria] = $this->splitCriteria(
            $this->normalizeCriteria($criteria, $criteriaHandler)
        );

        return match($operator) {
            'AND' => new AndCondition($criteria),
            'OR' => new OrCondition($criteria),
            default => parent::getCondition([$operator, ...$criteria], $criteriaHandler),
        };
    }
}
