<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Filter\AndX;
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
use Yiisoft\Data\Reader\Filter\OrX;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

abstract class BaseQueryDataReaderTestCase extends TestCase
{
    use DataTrait;

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
        $dataReader = (new QueryDataReader($query));

        $this->assertEquals($query->count(), $dataReader->count());
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
            'between' => [
                new Between('column', 100, 300),
                '[[column]] BETWEEN 100 AND 300',
            ],
            'greater than' => [
                new GreaterThan('column', 1000),
                '[[column]] > 1000',
            ],
            'greater than or equal' => [
                new GreaterThanOrEqual('column', 3.5),
                '[[column]] >= 3.5',
            ],
            'less than' => [
                new LessThan('column', 10.7),
                '[[column]] < 10.7',
            ],
            'less-than-or-equal' => [
                new LessThanOrEqual('column', 100),
                '[[column]] <= 100',
            ],
            'in' => [
                new In('column', [10, 20.5, 30]),
                '[[column]] IN (10, 20.5, 30)',
            ],
            'like' => [
                new Like('column', 'foo'),
                "[[column]] LIKE '%foo%'",
            ],
            // Not
            'not equals' => [
                new Not(new Equals('equals', 1)),
                '[[equals]] <> 1',
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
                '[[column]] < 3.5',
            ],
            'not less than' => [
                new Not(new LessThan('column', 10.7)),
                '[[column]] >= 10.7',
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
            'and, or' => [
                new AndX(
                    new EqualsNull('null_column'),
                    new Equals('equals', 10),
                    new Between('between', 10, 20),
                    new OrX(
                        new Equals('id', 8),
                        new Like('name', 'foo')
                    )
                ),
                '([[null_column]] IS NULL) AND ' .
                '([[equals]] = 10) AND ' .
                '([[between]] BETWEEN 10 AND 20) AND ' .
                "(([[id]] = 8) OR ([[name]] LIKE '%foo%'))",
            ],
            'or, and' => [
                new OrX(
                    new GreaterThan('greater_than', 15),
                    new LessThanOrEqual('less_than_or_equal', 10),
                    new Not(new Equals('not_equals', 'test')),
                    new AndX(
                        new Equals('id', 8),
                        new Like('name', 'bar')
                    )
                ),
                '([[greater_than]] > 15) OR ' .
                '([[less_than_or_equal]] <= 10) OR ' .
                "([[not_equals]] <> 'test') OR " .
                "(([[id]] = 8) AND ([[name]] LIKE '%bar%'))",
            ],
            'and, or 2' => [
                new AndX(
                    new GreaterThan('id', 88),
                    new OrX(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) AND (([[state]] = 2) OR ([[name]] LIKE '%eva%'))",
            ],
            'or, and 2' => [
                new OrX(
                    new GreaterThan('id', 88),
                    new AndX(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) OR (([[state]] = 2) AND ([[name]] LIKE '%eva%'))",
            ],
            'or, or' => [
                new OrX(
                    new GreaterThan('id', 88),
                    new OrX(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "([[id]] > 88) OR (([[state]] = 2) OR ([[name]] LIKE '%eva%'))",
            ],
            'and, and' => [
                new AndX(
                    new GreaterThan('id', 88),
                    new AndX(
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
