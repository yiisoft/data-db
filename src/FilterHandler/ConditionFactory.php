<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use LogicException;
use Yiisoft\Db\Query\QueryInterface;

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

        $operator = $criteria[0];
        if (!is_string($operator)) {
            throw new LogicException('Operator must be a string.');
        }

        $operands = array_slice($criteria, 1);

        return match ($operator) {
            'and', 'or' => self::makeGroup($operator, $operands),
            'not' => self::makeNot($operator, $operands),
            'like' => self::makeLike($operator, $operands),
            'between' => self::makeBetween($operator, $operands),
            'in' => self::makeIn($operator, $operands),
            'empty' => self::makeEmpty($operator, $operands),
            'null' => self::makeNull($operator, $operands),
            '=' => self::makeEquals($operator, $operands),
            '>', '<', '>=', '<=' => self::makeCompare($operator, $operands),
            'exists' => self::makeExists($operator, $operands),
            default => throw new LogicException(sprintf('Not supported operator: %s.', $operator)),
        };
    }

    private static function makeGroup(string $operator, array $operands): ?array
    {
        if (!array_key_exists(0, $operands)) {
            throw new LogicException(
                sprintf(
                    'Not found parameter for the "%s" operator.',
                    $operator,
                )
            );
        }
        if (!is_array($operands[0])) {
            throw new LogicException(
                sprintf(
                    'The parameter for "%s" operator must be an array. Got %s.',
                    $operator,
                    get_debug_type($operands[0])
                )
            );
        }
        if (empty($operands[0])) {
            return null;
        }
        $condition = [strtoupper($operator)];
        foreach ($operands[0] as $subCriteria) {
            if (!is_array($subCriteria)) {
                throw new LogicException('Incorrect sub-criteria.');
            }
            $condition[] = self::make($subCriteria);
        }
        return $condition;
    }

    private static function makeNot(string $operator, array $operands): ?array
    {
        if (
            array_keys($operands) !== [0]
            || !is_array($operands[0])
        ) {
            throw new LogicException('Incorrect criteria for the "not" operator.');
        }
        if (empty($operands[0])) {
            return null;
        }

        $subCondition = self::make($operands[0]);

        if (isset($subCondition[0]) && is_string($subCondition[0])) {
            $convert = [
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
            ];
            $operator = strtoupper($subCondition[0]);
            if (isset($convert[$operator])) {
                $subCondition[0] = $convert[$operator];
                return $subCondition;
            }
        }

        return ['NOT', $subCondition];
    }

    private static function makeLike(string $operator, array $criteria): array
    {
        if (
            array_keys($criteria) !== [0, 1]
            || !is_string($criteria[0])
            || !is_string($criteria[1])
        ) {
            throw new LogicException('Incorrect criteria for the "like" operator.');
        }
        return ['LIKE', $criteria[0], $criteria[1]];
    }

    private static function makeBetween(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0, 1, 2]
            || !is_string($operands[0])
            || !(is_scalar($operands[1]) || $operands[1] instanceof DateTimeInterface)
            || !(is_scalar($operands[2]) || $operands[2] instanceof DateTimeInterface)
        ) {
            throw new LogicException('Incorrect criteria for the "between" operator.');
        }
        $from = $operands[1] instanceof DateTimeInterface
            ? $operands[1]->format('Y-m-d H:i:s')
            : $operands[1];
        $to = $operands[2] instanceof DateTimeInterface
            ? $operands[2]->format('Y-m-d H:i:s')
            : $operands[2];
        return ['BETWEEN', $operands[0], $from, $to];
    }

    private static function makeIn(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || !is_array($operands[1])
        ) {
            throw new LogicException('Incorrect criteria for the "in" operator.');
        }
        return ['IN', $operands[0], $operands[1]];
    }

    private static function makeEmpty(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0]
            || !is_string($operands[0])
        ) {
            throw new LogicException('Incorrect criteria for the "empty" operator.');
        }
        return ['OR', ['IS', $operands[0], null], ['=', $operands[0], '']];
    }

    private static function makeNull(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0]
            || !is_string($operands[0])
        ) {
            throw new LogicException('Incorrect criteria for the "empty" operator.');
        }
        return ['IS', $operands[0], null];
    }

    private static function makeEquals(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || (
                !is_string($operands[1])
                && !(is_scalar($operands[1]) || is_null($operands[1]) || $operands[1] instanceof DateTimeInterface)
            )
        ) {
            throw new LogicException('Incorrect criteria for the "=" operator.');
        }

        if ($operands[1] === null) {
            return ['IS NULL', $operands[0]];
        }

        $value = $operands[1] instanceof DateTimeInterface
            ? $operands[1]->format('Y-m-d H:i:s')
            : $operands[1];

        return ['=', $operands[0], $value];
    }

    private static function makeCompare(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || (
                !is_string($operands[1])
                && !(is_scalar($operands[1]) || $operands[1] instanceof DateTimeInterface)
            )
        ) {
            throw new LogicException(sprintf('Incorrect criteria for the "%s" operator.', $operator));
        }

        $value = $operands[1] instanceof DateTimeInterface
            ? $operands[1]->format('Y-m-d H:i:s')
            : $operands[1];

        return [$operator, $operands[0], $value];
    }

    private static function makeExists(string $operator, array $operands): array
    {
        if (
            array_keys($operands) !== [0]
            || !$operands[0] instanceof QueryInterface
        ) {
            throw new LogicException('Incorrect criteria for the "exists" operator.');
        }

        return ['EXISTS', $operands[0]];
    }
}
