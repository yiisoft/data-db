<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;

/**
 * @internal
 */
final class ConditionFactory
{
    public static function make(array $criteria): ?array
    {
        if (!isset($criteria[0])) {
            throw new LogicException('Incorrect criteria array.');
        }

        switch ($criteria[0]) {
            case 'and':
            case 'or':
                /** @psalm-var string $criteria[0] */
                if (!array_key_exists(1, $criteria)) {
                    throw new LogicException(
                        sprintf(
                            'Not found second parameter for the "%s" operator.',
                            $criteria[0],
                        )
                    );
                }
                if (!is_array($criteria[1])) {
                    throw new LogicException(
                        sprintf(
                            'The second parameter for "%s" operator must be an array. Got %s.',
                            $criteria[0],
                            get_debug_type($criteria[1])
                        )
                    );
                }
                if (empty($criteria[1])) {
                    return null;
                }
                $condition = [$criteria[0]];
                foreach ($criteria[1] as $subCriteria) {
                    if (!is_array($subCriteria)) {
                        throw new LogicException('Incorrect sub-criteria.');
                    }
                    $condition[] = self::make($subCriteria);
                }
                return $condition;

            case 'like':
                if (array_keys($criteria) !== [0, 1, 2] || !is_string($criteria[1]) || !is_string($criteria[2])) {
                    throw new LogicException('Incorrect criteria for the "like" operator.');
                }
                return ['like', $criteria[1], $criteria[2]];
        }

        throw new LogicException(sprintf('Not supported operator: %s.', (string) $criteria[0]));
    }
}
