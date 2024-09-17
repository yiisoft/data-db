<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\CustomerDataReader;
use Yiisoft\Data\Db\Tests\Support\CustomerDTO;
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
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

abstract class QueryDataReaderTest extends TestCase
{
    public function testDataReader(): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');
        $dataReader = new QueryDataReader($query);

        $this->assertSame(
            $query->createCommand()->getRawSql(),
            $dataReader->getPreparedQuery()->createCommand()->getRawSql()
        );
    }

    abstract public static function dataOffset(): array;

    #[DataProvider('dataOffset')]
    public function testOffset(string $expectedSql): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withOffset(2);
        $query->offset(2);

        $this->assertSame($expectedSql, $dataReader->getPreparedQuery()->createCommand()->getRawSql());
        $this->assertSame($expectedSql, $query->createCommand()->getRawSql());
    }

    public static function dataLimit(): array
    {
        return [
            [
                'SELECT * FROM {{%customer}} LIMIT 1 OFFSET 1',
            ],
        ];
    }

    #[DataProvider('dataLimit')]
    public function testLimit(string $expectedSql): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withOffset(1)->withLimit(1);
        $query->offset(1)->limit(1);
        $expectedSql = $db->getQuoter()->quoteSql($expectedSql);

        $this->assertSame($expectedSql, $dataReader->getPreparedQuery()->createCommand()->getRawSql());
        $this->assertSame($expectedSql, $query->createCommand()->getRawSql());
    }

    public static function dataSort(): array
    {
        return [
            'with order string, 2 fields, desc and asc' => [
                Sort::only([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,email'),
                '[[name]] DESC, [[email]]',
            ],
            'with order string, 2 fields, desc and desc' => [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,-email'),
                '[[name]] DESC, [[email]] DESC',
            ],
            'with order string, 1 field, desc' => [
                Sort::any([
                    'name',
                    'email',
                ])
                    ->withOrderString('-email'),
                '[[email]] DESC, [[name]]',
            ],
            'with order string, 1 field desc, without default sorting' => [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withoutDefaultSorting()
                ->withOrderString('-email'),
                '[[email]] DESC',
            ],
            'with order string, 1 field desc, expression' => [
                Sort::any([
                    'name' => [
                        'asc' => [
                            new Expression('[[name]] ASC NULLS FIRST'),
                        ],
                        'desc' => [
                            new Expression('[[name]] DESC NULLS LAST'),
                        ],
                    ],
                ])
                ->withOrderString('-name'),
                '[[name]] DESC NULLS LAST',
            ],
        ];
    }

    #[DataProvider('dataSort')]
    public function testSort(Sort $sort, string $expectedSql): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withSort($sort);
        $expectedSql = $db->getQuoter()->quoteSql($expectedSql);

        $this->assertStringEndsWith(
            $expectedSql,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    public function testCount(): void
    {
        $query = (new Query($this->getConnection()))->from('customer');
        $dataReader = (new CustomerDataReader($query));

        $this->assertEquals($query->count(), $dataReader->count());
    }

    public function testDtoCreateItem(): void
    {
        $query = (new Query($this->getConnection()))->from('customer');
        $dataReader = (new CustomerDataReader($query))
            ->withBatchSize(null);

        $this->assertInstanceOf(CustomerDTO::class, $dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            $this->assertInstanceOf(CustomerDTO::class, $row);
        }
    }

    public function testArrayCreateItem(): void
    {
        $query = (new Query($this->getConnection()))->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withBatchSize(null);

        $this->assertIsArray($dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            $this->assertIsArray($row);
        }
    }

    public static function dataFilterSql(): array
    {
        return [
            // Simple
            'equals' => [
                new Equals('equals', 1),
                '[[equals]] = 1',
            ],
            'equals datetime' => [
                new Equals('column', new DateTime('2011-01-01T15:03:01.012345Z')),
                "[[column]] = '2011-01-01 15:03:01'",
            ],
            'between' => [
                new Between('column', 100, 300),
                '[[column]] BETWEEN 100 AND 300',
            ],
            'between dates' => [
                new Between('column', new DateTime('2011-01-01T15:00:01'), new DateTime('2011-01-01T15:10:01')),
                "[[column]] BETWEEN '2011-01-01 15:00:01' AND '2011-01-01 15:10:01'",
            ],
            'greater than' => [
                new GreaterThan('column', 1000),
                '[[column]] > 1000',
            ],
            'greater than date' => [
                new GreaterThan('column', new DateTime('2011-01-01T15:00:01')),
                "[[column]] > '2011-01-01 15:00:01'",
            ],
            'greater than or equal' => [
                new GreaterThanOrEqual('column', 3.5),
                '[[column]] >= \'3.5\'',
            ],
            'less than' => [
                new LessThan('column', 10.7),
                '[[column]] < \'10.7\'',
            ],
            'less-than-or-equal' => [
                new LessThanOrEqual('column', 100),
                '[[column]] <= 100',
            ],
            'in' => [
                new In('column', [10, 20.5, 30]),
                '[[column]] IN (10, \'20.5\', 30)',
            ],
            'like' => [
                new Like('column', 'foo'),
                "[[column]] LIKE '%foo%'",
            ],
            // Not
            'not equals' => [
                new Not(new Equals('equals', 1)),
                '[[equals]] != 1',
            ],
            'not between' => [
                new Not(new Between('column', 100, 300)),
                '[[column]] NOT BETWEEN 100 AND 300',
            ],
            'not greater than' => [
                new Not(new GreaterThan('column', 1000)),
                '[[column]] <= 1000',
            ],
            'not greater than or equal' => [
                new Not(new GreaterThanOrEqual('column', 3.5)),
                '[[column]] < \'3.5\'',
            ],
            'not less than' => [
                new Not(new LessThan('column', 10.7)),
                '[[column]] >= \'10.7\'',
            ],
            'not less than or equal' => [
                new Not(new LessThanOrEqual('column', 100)),
                '[[column]] > 100',
            ],
            'not in' => [
                new Not(new In('column', [10, 20, 30])),
                '[[column]] NOT IN (10, 20, 30)',
            ],
            'not like' => [
                new Not(new Like('column', 'foo')),
                "[[column]] NOT LIKE '%foo%'",
            ],
            // Group
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
                '([[null_column]] IS NULL) AND ' .
                '([[equals]] = 10) AND ' .
                '([[between]] BETWEEN 10 AND 20) AND ' .
                "(([[id]] = 8) OR ([[name]] LIKE '%foo%'))",
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
                '([[greater_than]] > 15) OR ' .
                '([[less_than_or_equal]] <= 10) OR ' .
                "([[not_equals]] != 'test') OR " .
                "(([[id]] = 8) AND ([[name]] LIKE '%bar%'))",
            ],
            'all, any 2' => [
                new All(
                    new GreaterThan('id', 88),
                    new Any(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) AND (([[state]] = 2) OR ([[name]] LIKE '%eva%'))",
            ],
            'any, all 2' => [
                new Any(
                    new GreaterThan('id', 88),
                    new All(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) OR (([[state]] = 2) AND ([[name]] LIKE '%eva%'))",
            ],
            'any, any' => [
                new Any(
                    new GreaterThan('id', 88),
                    new Any(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) OR (([[state]] = 2) OR ([[name]] LIKE '%eva%'))",
            ],
            'all, all' => [
                new All(
                    new GreaterThan('id', 88),
                    new All(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) AND (([[state]] = 2) AND ([[name]] LIKE '%eva%'))",
            ],
        ];
    }

    #[DataProvider('dataFilterSql')]
    public function testWhereSql(FilterInterface $filter, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withFilter($filter);
        $expectedSql = 'SELECT * FROM {{%customer}} WHERE ' . $condition;
        $expectedSql = $db->getQuoter()->quoteSql($expectedSql);

        $this->assertStringEndsWith(
            $expectedSql,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    #[DataProvider('dataFilterSql')]
    public function testHavingSql(FilterInterface $having, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withHaving($having);
        $expectedSql = 'SELECT * FROM {{%customer}} HAVING ' . $condition;
        $expectedSql = $db->getQuoter()->quoteSql($expectedSql);

        $this->assertSame(
            $expectedSql,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }
}
