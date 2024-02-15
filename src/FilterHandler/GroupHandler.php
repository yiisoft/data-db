<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;

/**
 * @internal
 */
abstract class GroupHandler implements QueryHandlerInterface
{
    public function getCondition(array $operands, Context $context): ?array
    {
        if (!array_key_exists(0, $operands)) {
            throw new LogicException(
                sprintf(
                    'Not found parameter for the "%s" operator.',
                    $this->getOperator(),
                )
            );
        }
        if (!is_array($operands[0])) {
            throw new LogicException(
                sprintf(
                    'The parameter for "%s" operator must be an array. Got %s.',
                    $this->getOperator(),
                    get_debug_type($operands[0])
                )
            );
        }
        if (empty($operands[0])) {
            return null;
        }
        $condition = [strtoupper($this->getOperator())];
        foreach ($operands[0] as $subCriteria) {
            if (!is_array($subCriteria)) {
                throw new LogicException('Incorrect sub-criteria.');
            }
            $condition[] = $context->handleCriteria($subCriteria);
        }
        return $condition;
    }
}
