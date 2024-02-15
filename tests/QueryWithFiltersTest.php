<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\Between;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\Filter\Equals;
use Yiisoft\Data\Db\Filter\GreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\GroupFilter;
use Yiisoft\Data\Db\Filter\In;
use Yiisoft\Data\Db\Filter\IsNull;
use Yiisoft\Data\Db\Filter\LessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like;
use Yiisoft\Data\Db\Filter\Not;
use Yiisoft\Data\Db\Filter\NotEquals;
use Yiisoft\Data\Db\Filter\OrLike;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\Any;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

final class QueryWithFiltersTest extends TestCase
{
    use TestTrait;

    public static function setUpBeforeClass(): void
    {
        CompareFilter::$mainDateTimeFormat = 'Y-m-d H:i:s';
    }

    public function simpleDataProvider(): array
    {
        return [
            //EqualsHandler
            [
                new Equals('equals', 1),
                '[equals] = 1',
            ],
            [
                new Equals('equals', [1, 2, 3]),
                '[equals] IN (1, 2, 3)',
            ],
            [
                new Equals('column', new DateTime('2011-01-01T15:03:01.012345Z')),
                "[column] = '2011-01-01 15:03:01'",
            ],
            //BetweenHandler
            [
                new Between('column', [100, 300]),
                '[column] BETWEEN 100 AND 300',
            ],
            [
                new Between('column', [100, null]),
                '[column] >= 100',
            ],
            [
                new Between('column', [null, 250]),
                '[column] <= 250',
            ],
            [
                new Between('column', [new DateTime('2011-01-01T15:00:01'), new DateTime('2011-01-01T15:10:01')]),
                "[column] BETWEEN '2011-01-01 15:00:01' AND '2011-01-01 15:10:01'",
            ],
            //GreaterThanHandler
            [
                new GreaterThan('column', 1000),
                '[column] > 1000',
            ],
            [
                new GreaterThan('column', new DateTime('2011-01-01T15:00:01')),
                "[column] > '2011-01-01 15:00:01'",
            ],
            [
                new GreaterThanOrEqual('column', 3.5),
                '[column] >= 3.5',
            ],
            [
                new LessThan('column', 10.7),
                '[column] < 10.7',
            ],
            [
                new LessThanOrEqual('column', 100),
                '[column] <= 100',
            ],
            [
                new In('column', [10, 20.5, 30]),
                '[column] IN (10, 20.5, 30)',
            ],
            //NotHandler equals
            [
                new NotEquals('column', 40),
                '[column] != 40',
            ],
            //LikeHandler
            [
                new Like('column', 'foo'),
                "[column] LIKE '%foo%'",
            ],
            [
                (new Like('column', 'foo'))->withoutStart(),
                "[column] LIKE 'foo%'",
            ],
            [
                (new Like('column', 'foo'))->withoutEnd(),
                "[column] LIKE '%foo'",
            ],
            [
                (new Like('column', 'foo'))->withoutBoth(),
                "[column] LIKE 'foo'",
            ],
            [
                new Like('column', new Expression("CONCAT([[foo]] ->> 'bar', '%')")),
                "[column] LIKE CONCAT([foo] ->> 'bar', '%')",
            ],
            //Array Or FilterLike
            [
                new OrLike('column', ['foo', 'bar']),
                "[column] LIKE '%foo%' OR [column] LIKE '%bar%'",
            ],
        ];
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testSimpleFilter(CompareFilter $filter, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withFilter($filter);

        $expected = 'SELECT * FROM [customer] WHERE ' . $condition;

        $this->assertSame(
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
            $expected,
        );
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testSimpleHaving(CompareFilter $having, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withHaving($having);

        $expected = 'SELECT * FROM [customer] HAVING ' . $condition;

        $this->assertSame(
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
            $expected,
        );
    }

    public function groupFilterDataProvider(): array
    {
        return [
            [
                new All(
                    new IsNull('null_column'),
                    new Equals('equals', 10),
                    new Between('between', [10, 20]),
                    new Any(
                        new Equals('id', 8),
                        new Like('name', 'foo')
                    )
                ),
                "([null_column] IS NULL) AND ([equals] = 10) AND ([between] BETWEEN 10 AND 20) AND (([id] = 8) OR ([name] LIKE '%foo%'))",
            ],
            [
                new Any(
                    new GreaterThan('greater_than', 15),
                    new LessThanOrEqual('less_than_or_equal', 10),
                    new Not(new Equals('not_equals', 'test')),
                    new All(
                        new Equals('id', 8),
                        new Like('name', 'bar')
                    )
                ),
                "([greater_than] > 15) OR ([less_than_or_equal] <= 10) OR ([not_equals] != 'test') OR (([id] = 8) AND ([name] LIKE '%bar%'))",
            ],
            [
                All::fromCriteriaArray([
                    ['>', 'id', 88],
                    [
                        'or',
                        ['=', 'state', 2],
                        ['like', 'name', 'eva'],
                    ],
                ]),
                "([id] > 88) AND (([state] = 2) OR ([name] LIKE '%eva%'))",
            ],
            [
                Any::fromCriteriaArray([
                    ['>', 'id', 88],
                    [
                        'and',
                        ['=', 'state', 2],
                        ['like', 'name', 'eva'],
                    ],
                ]),
                "([id] > 88) OR (([state] = 2) AND ([name] LIKE '%eva%'))",
            ],
            [
                (new Any())->withCriteriaArray([
                    ['>', 'id', 88],
                    [
                        'or',
                        ['=', 'state', 2],
                        ['like', 'name', 'eva'],
                    ],
                ]),
                "([id] > 88) OR (([state] = 2) OR ([name] LIKE '%eva%'))",
            ],
            [
                (new All())->withCriteriaArray([
                    ['>', 'id', 88],
                    [
                        'and',
                        ['=', 'state', 2],
                        ['like', 'name', 'eva'],
                    ],
                ]),
                "([id] > 88) AND (([state] = 2) AND ([name] LIKE '%eva%'))",
            ],
        ];
    }

    /**
     * @dataProvider groupFilterDataProvider
     */
    public function testGroupFilter(GroupFilter $filter, string $expected): void
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
