<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;

/**
 * @internal
 */
abstract class GroupHandler implements QueryHandlerInterface
{
    public function getCondition(array $operands, Context $context): ?Condition
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

        $body = [strtoupper($this->getOperator())];
        $params = [];

        foreach ($operands[0] as $subCriteria) {
            if (!is_array($subCriteria)) {
                throw new LogicException('Incorrect sub-criteria.');
            }
            $condition = $context->handleCriteria($subCriteria);
            if ($condition !== null) {
                $body[] = $condition->body;
                $params = array_merge($params, $condition->params);
            }
        }
        return new Condition($body, $params);
    }
}
