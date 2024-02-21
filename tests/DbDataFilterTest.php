<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\All;
use Yiisoft\Data\Db\Filter\Any;
use Yiisoft\Data\Db\Filter\Between;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\Filter\Equals;
use Yiisoft\Data\Db\Filter\EqualsEmpty;
use Yiisoft\Data\Db\Filter\Exists;
use Yiisoft\Data\Db\Filter\GreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\ILike;
use Yiisoft\Data\Db\Filter\In;
use Yiisoft\Data\Db\Filter\LessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like;
use Yiisoft\Data\Db\Filter\OrILike;
use Yiisoft\Data\Db\Filter\OrLike;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Data\Reader\Filter\Not;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

final class DbDataFilterTest extends TestCase
{
    use TestTrait;

    public static function simpleDataProvider(): array
    {
        return [
            'equals' => [
                new Equals('equals', 1),
                '[equals] = 1',
            ],
            'equals-null' => [
                new Equals('equals', null),
                '[equals] IS NULL',
            ],
            'equals-in' => [
                new Equals('equals', [1,2,3]),
                '[equals] IN (1, 2, 3)',
            ],
            'equals-expression' => [
                new Equals(new Expression('[[json]] #> [[key]]'), 1),
                '[json] #> [key] = 1',
            ],
            'between' => [
                new Between('column', 100, 200),
                '[column] BETWEEN 100 AND 200',
            ],
            'between-wo-min' => [
                new Between('column', null, 200),
                '[column] <= 200',
            ],
            'between-wo-max' => [
                new Between('column', 100, null),
                '[column] >= 100',
            ],
            'between-empty' => [
                new Between('column', null, ''),
                '',
            ],
            'greater-than' => [
                new GreaterThan('column', 10),
                '[column] > 10',
            ],
            'greater-than-or-equal' => [
                new GreaterThanOrEqual('column', 20),
                '[column] >= 20',
            ],
            'less-than' => [
                new LessThan('column', 50),
                '[column] < 50',
            ],
            'less-than-or-equal' => [
                new LessThanOrEqual('column', 75),
                '[column] <= 75',
            ],
            'in' => [
                new In('id', [1, 2, 3, 5, 4]),
                '[id] IN (1, 2, 3, 5, 4)',
            ],
            'like' => [
                new Like('text', 'ex'),
                "[text] LIKE '%ex%'",
            ],
            'like-expression' => [
                new Like(
                    new Expression('json #> :columns'),
                    new Expression("CONCAT('%', json -> :column, '%')"),
                    [
                        ':columns' => '{foo, bar}',
                        ':column' => 'text-column',
                    ]
                ),
                "json #> '{foo, bar}' LIKE CONCAT('%', json -> 'text-column', '%')",
            ],
            'like-wo-start' => [
                (new Like('text', 'te'))->withoutStart(),
                "[text] LIKE 'te%'",
            ],
            'like-wo-end' => [
                (new Like('text', 'xt'))->withoutEnd(),
                "[text] LIKE '%xt'",
            ],
            'like-wo-both' => [
                (new Like('text', 'text'))->withoutBoth(),
                "[text] LIKE 'text'",
            ],
            'like-with-both' => [
                (new Like('text', 'text'))->withoutBoth()->withBoth(),
                "[text] LIKE '%text%'",
            ],
            'ilike' => [
                new ILike('TEXT', 'ex'),
                "[TEXT] ILIKE '%ex%'",
            ],
            'ilike-wo-start' => [
                (new ILike('TEXT', 'te'))->withoutStart(),
                "[TEXT] ILIKE 'te%'",
            ],
            'ilike-wo-end' => [
                (new ILike('TEXT', 'xt'))->withoutEnd(),
                "[TEXT] ILIKE '%xt'",
            ],
            'ilike-wo-both' => [
                (new ILike(new Expression('[[TEXT]]'), 'text'))->withoutBoth(),
                "[TEXT] ILIKE 'text'",
            ],
            'or-like' => [
                new OrLike('text', ['te', 'xt']),
                "[text] LIKE '%te%' OR [text] LIKE '%xt%'",
            ],
            'or-ilike' => [
                new OrILike(new Expression("CONCAT_WS(' ', [[column_1]], [[column_2]])"), ['te', 'xt']),
                "CONCAT_WS(' ', [column_1], [column_2]) ILIKE '%te%' OR CONCAT_WS(' ', [column_1], [column_2]) ILIKE '%xt%'",
            ],
            'equals-empty' => [
                new EqualsEmpty('empty_column', '', '0'),
                "([empty_column] IS NULL) OR ([empty_column] IN ('', '0'))",
            ],
        ];
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testSimpleFilter(FilterInterface $filter, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withFilter($filter);

        if ($condition) {
            $expected = 'SELECT * FROM [customer] WHERE ' . $condition;
        } else {
            $expected = 'SELECT * FROM [customer]';
        }

        self::assertSame(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    public function testExistsFilter(): void
    {
        $db = $this->getConnection();
        $subQuery = (new Query($db))
            ->select(new Expression('1'))
            ->from('some_table');

        $filter = new Exists($subQuery);

        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withFilter($filter);

        $expected = 'SELECT * FROM [customer] WHERE EXISTS (SELECT 1 FROM [some_table])';

        $this->assertSame(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );

        $expected = 'SELECT * FROM [customer] WHERE NOT EXISTS (SELECT 1 FROM [some_table])';
        $query = (new Query($db))
            ->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withFilter(new Not($filter));

        self::assertSame(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    public static function groupFilterDataProvider(): array
    {
        return [
            'all' => [
                new All(
                    new Equals('equals', 2),
                    new Any(
                        new LessThan('less', 10),
                        new Like('like', 'bar'),
                    ),
                ),
                "([equals] = 2) AND (([less] < 10) OR ([like] LIKE '%bar%'))",
            ],
            'any' => [
                new Any(
                    new Equals('equals', 5),
                    new In('in_column', ['foo', 'bar']),
                    new All(
                        new GreaterThanOrEqual('greater', 10),
                        new Not(new Like('like', 'foo')),
                    ),
                ),
                "([equals] = 5) OR ([in_column] IN ('foo', 'bar')) OR (([greater] >= 10) AND ([like] NOT LIKE '%foo%'))",
            ],
        ];
    }

    /**
     * @dataProvider groupFilterDataProvider
     * @param FilterInterface $filter
     * @param string $condition
     * @return void
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     */
    public function testGroupFilter(FilterInterface $filter, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withFilter($filter);
        $expected = 'SELECT * FROM [customer] WHERE ' . $condition;

        self::assertSame(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    public function testEqualsEmptyException(): void
    {
        new EqualsEmpty('column', 0, false, '');
        self::assertTrue(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$value must be equal php "empty". "empty" given.');

        new EqualsEmpty('column', 'empty');
    }

    public function testInQueryFilter(): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $inQuery = (new Query($db))->select('id')->from('other_table')->where('group_id = :group');
        $filter = new In('id', $inQuery, [
            ':group' => 10,
        ]);

        $dataReader = (new QueryDataReader($query))->withFilter($filter);
        $expected = 'SELECT * FROM [customer] WHERE [id] IN (SELECT [id] FROM [other_table] WHERE group_id = 10)';

        self::assertSame(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    public static function nullableFilterDataProvider(): array
    {
        return [
            [
                new Equals('column', null),
                ['IS', 'column', null],
            ],
            [
                new GreaterThan('greater', null),
                ['IS', 'greater', null],
            ],
            [
                new GreaterThanOrEqual('greater-or-equal', null),
                ['IS', 'greater-or-equal', null],
            ],
            [
                new LessThan('less-than', null),
                ['IS', 'less-than', null],
            ],
            [
                new LessThanOrEqual('less-than-or-equal', null),
                ['IS', 'less-than-or-equal', null],
            ],
            [
                new Like('text', null),
                ['IS', 'text', null],
            ],
        ];
    }

    /**
     * @dataProvider nullableFilterDataProvider
     * @param CompareFilter $filter
     * @param array $criteria
     * @return void
     */
    public function testIgnoreNull(CompareFilter $filter, array $criteria): void
    {
        $ignoreFilter = $filter->withIgnoreNull();
        $sameFilter = $filter->withIgnoreNull(false);

        self::assertTrue($sameFilter === $filter);
        self::assertFalse($ignoreFilter === $filter);
        self::assertSame([], $ignoreFilter->toCriteriaArray());
        self::assertSame($criteria, $filter->toCriteriaArray());
    }

    public function testGroupWithCriteria(): void
    {
        $filter = new All();
        $newFilter = $filter->withCriteriaArray([
            ['>', 'test', 1],
            ['<', 'test', 5],
        ]);


        self::assertFalse($filter === $newFilter);
        self::assertSame(
            [
                'and',
                ['>', 'test', 1],
                ['<', 'test', 5]
            ],
            $newFilter->toCriteriaArray()
        );
    }

    public static function groupCriteriaExceptionDataProvider(): array
    {
        return [
            [
                ['OR', ['>', 'test', 1]],
                InvalidArgumentException::class,
                'Invalid filter on "0" key.',
            ],
            [
                [
                    'ALL' => [
                        'test' => ['<', 5],
                    ],
                ],
                InvalidArgumentException::class,
                'Invalid filter operator on "ALL" key.'
            ]
        ];
    }

    /**
     * @dataProvider groupCriteriaExceptionDataProvider
     * @param array $criteria
     * @param string $exception
     * @param string $message
     * @return void
     */
    public function testGroupWithCriteriaExceptions(array $criteria, string $exception, string $message): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        All::fromCriteriaArray($criteria);
    }

    public function testMatchFilter(): void
    {
        $filter = new Like('column', 0.3);
        $withoutEnd = $filter->withoutEnd();
        $withoutStart = $filter->withoutStart();

        self::assertFalse($filter === $withoutEnd);
        self::assertTrue($withoutEnd === $withoutEnd->withoutEnd());
        self::assertTrue($filter === $filter->withEnd());
        self::assertFalse($filter === $withoutEnd->withEnd());
        self::assertFalse($filter === $withoutStart);
        self::assertTrue($withoutStart === $withoutStart->withoutStart());
        self::assertTrue($filter === $filter->withStart());
        self::assertFalse($filter === $withoutStart->withStart());
        self::assertTrue($filter === $filter->withBoth());
        self::assertFalse($filter === $filter->withoutBoth());
        self::assertSame(
            ['like', 'column', 0.3],
            $filter->toCriteriaArray(),
        );
        self::assertSame(
            ['like', 'column', '%0.3', null],
            $filter->withoutEnd()->toCriteriaArray(),
        );
        self::assertSame(
            ['like', 'column', '0.3%', null],
            $filter->withoutStart()->toCriteriaArray(),
        );
        self::assertSame(
            ['like', 'column', 0.3, null],
            $filter->withoutBoth()->toCriteriaArray(),
        );
    }
}
