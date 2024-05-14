<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\Any;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\EqualsNull;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\Not;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\Query;

abstract class QueryWithFiltersTest extends TestCase
{
    public static function simpleDataProvider(): array
    {
        return [
            'equals' => [
                new Equals('equals', 1),
                '`equals` = 1',
            ],
            'equals-datetime' => [
                new Equals('column', new DateTime('2011-01-01T15:03:01.012345Z')),
                "`column` = '2011-01-01 15:03:01'",
            ],
            'between' => [
                new Between('column', 100, 300),
                '`column` BETWEEN 100 AND 300',
            ],
            'between-dates' => [
                new Between('column', new DateTime('2011-01-01T15:00:01'), new DateTime('2011-01-01T15:10:01')),
                "`column` BETWEEN '2011-01-01 15:00:01' AND '2011-01-01 15:10:01'",
            ],
            'greater-than' => [
                new GreaterThan('column', 1000),
                '`column` > 1000',
            ],
            'greater-than-date' => [
                new GreaterThan('column', new DateTime('2011-01-01T15:00:01')),
                "`column` > '2011-01-01 15:00:01'",
            ],
            [
                new GreaterThanOrEqual('column', 3.5),
                '`column` >= \'3.5\'',
            ],
            [
                new LessThan('column', 10.7),
                '`column` < \'10.7\'',
            ],
            [
                new LessThanOrEqual('column', 100),
                '`column` <= 100',
            ],
            [
                new In('column', [10, 20.5, 30]),
                '`column` IN (10, \'20.5\', 30)',
            ],
            'like' => [
                new Like('column', 'foo'),
                "`column` LIKE '%foo%' ESCAPE '\'",
            ],
        ];
    }

    #[DataProvider('simpleDataProvider')]
    public function testSimpleFilter(FilterInterface $filter, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withFilter($filter);

        $expected = 'SELECT * FROM `customer` WHERE ' . $condition;

        $this->assertSame(
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
            $expected,
        );
    }

    #[DataProvider('simpleDataProvider')]
    public function testSimpleHaving(FilterInterface $having, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withHaving($having);

        $expected = 'SELECT * FROM `customer` HAVING ' . $condition;

        $this->assertSame(
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
            $expected,
        );
    }

    public static function groupFilterDataProvider(): array
    {
        return [
            'all, any' => [
                new All(
                    new EqualsNull('null_column'),
                    new Equals('equals', 10),
                    new Between('between', 10, 20),
                    new Any(
                        new Equals('id', 8),
                        new Like('name', 'foo')
                    )
                ),
                '(`null_column` IS NULL) AND ' .
                '(`equals` = 10) AND ' .
                '(`between` BETWEEN 10 AND 20) AND ' .
                "((`id` = 8) OR (`name` LIKE '%foo%'))",
            ],
            'any, all' => [
                new Any(
                    new GreaterThan('greater_than', 15),
                    new LessThanOrEqual('less_than_or_equal', 10),
                    new Not(new Equals('not_equals', 'test')),
                    new All(
                        new Equals('id', 8),
                        new Like('name', 'bar')
                    )
                ),
                '(`greater_than` > 15) OR ' .
                '(`less_than_or_equal` <= 10) OR ' .
                "(`not_equals` != 'test') OR " .
                "((`id` = 8) AND (`name` LIKE '%bar%'))",
            ],
            'all, any 2' => [
                new All(
                    new GreaterThan('id', 88),
                    new Any(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) AND ((`state` = 2) OR (`name` LIKE '%eva%' ESCAPE '\'))",
            ],
            'any, all 2' => [
                new Any(
                    new GreaterThan('id', 88),
                    new All(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) OR ((`state` = 2) AND (`name` LIKE '%eva%' ESCAPE '\'))",
            ],
            'any, any' => [
                new Any(
                    new GreaterThan('id', 88),
                    new Any(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) OR ((`state` = 2) OR (`name` LIKE '%eva%' ESCAPE '\'))",
            ],
            'all, all' => [
                new All(
                    new GreaterThan('id', 88),
                    new All(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) AND ((`state` = 2) AND (`name` LIKE '%eva%' ESCAPE '\'))",
            ],
        ];
    }

    #[DataProvider('groupFilterDataProvider')]
    public function testGroupFilter(All|Any $filter, string $expected): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withFilter($filter);

        $this->assertStringEndsWith(
            'WHERE ' . $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql()
        );
    }
}
