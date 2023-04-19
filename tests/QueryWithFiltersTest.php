<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\Between as FilterBetween;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\Filter\Equals as FilterEquals;
use Yiisoft\Data\Db\Filter\GreaterThan as FilterGreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual as FilterGreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\In as FilterIn;
use Yiisoft\Data\Db\Filter\LessThan as FilterLessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual as FilterLessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like as FilterLike;
use Yiisoft\Data\Db\Filter\NotEquals as FilterNotEquals;
use Yiisoft\Data\Db\Filter\OrLike as FilterOrLike;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
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
                new FilterEquals('equals', 1),
                '[equals] = 1',
            ],
            [
                new FilterEquals('equals', [1, 2, 3]),
                '[equals] IN (1, 2, 3)',
            ],
            [
                new FilterEquals('column', new DateTime('2011-01-01T15:03:01.012345Z')),
                "[column] = '2011-01-01 15:03:01'",
            ],
            //BetweenHandler
            [
                new FilterBetween('column', [100, 300]),
                '[column] BETWEEN 100 AND 300',
            ],
            [
                new FilterBetween('column', [100, null]),
                '[column] >= 100',
            ],
            [
                new FilterBetween('column', [null, 250]),
                '[column] <= 250',
            ],
            [
                new FilterBetween('column', [new DateTime('2011-01-01T15:00:01'), new DateTime('2011-01-01T15:10:01')]),
                "[column] BETWEEN '2011-01-01 15:00:01' AND '2011-01-01 15:10:01'",
            ],
            //GreaterThanHandler
            [
                new FilterGreaterThan('column', 1000),
                '[column] > 1000',
            ],
            [
                new FilterGreaterThan('column', new DateTime('2011-01-01T15:00:01')),
                "[column] > '2011-01-01 15:00:01'",
            ],
            [
                new FilterGreaterThanOrEqual('column', 3.5),
                '[column] >= 3.5',
            ],
            [
                new FilterLessThan('column', 10.7),
                '[column] < 10.7',
            ],
            [
                new FilterLessThanOrEqual('column', 100),
                '[column] <= 100',
            ],
            [
                new FilterIn('column', [10, 20.5, 30]),
                '[column] IN (10, 20.5, 30)',
            ],
            //NotHandler equals
            [
                new FilterNotEquals('column', 40),
                '[column] != 40',
            ],
            //LikeHandler
            [
                new FilterLike('column', 'foo'),
                "[column] LIKE '%foo%'",
            ],
            [
                (new FilterLike('column', 'foo'))->withoutStart(),
                "[column] LIKE 'foo%'",
            ],
            [
                (new FilterLike('column', 'foo'))->withoutEnd(),
                "[column] LIKE '%foo'",
            ],
            [
                (new FilterLike('column', 'foo'))->withoutBoth(),
                "[column] LIKE 'foo'",
            ],
            [
                new FilterLike('column', new Expression("CONCAT([[foo]] ->> 'bar', '%')")),
                "[column] LIKE CONCAT([foo] ->> 'bar', '%')",
            ],
            //Array Or FilterLike
            [
                new FilterOrLike('column', ['foo', 'bar']),
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
}
