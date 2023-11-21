<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\Not;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\Query;

final class DataFilterTest extends TestCase
{
    use TestTrait;

    public function simpleDataProvider(): array
    {
        return [
            //EqualsHandler
            [
                new Equals('equals', 1),
                '[equals] = 1',
            ],

            //BetweenHandler
            [
                new Between('column', 100, 300),
                '[column] BETWEEN 100 AND 300',
            ],
            //GreaterThanHandler
            [
                new GreaterThan('column', 1000),
                '[column] > 1000',
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
                new In('column', [10, 20, 30]),
                '[column] IN (10, 20, 30)',
            ],
            //LikeHandler
            [
                new Like('column', 'foo'),
                "[column] LIKE '%foo%'",
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

        $expected = 'SELECT * FROM [customer] WHERE ' . $condition;

        $this->assertSame(
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
            $expected,
        );
    }

    public function notDataProvider(): array
    {
        $filters = $this->simpleDataProvider();

        foreach ($filters as $i => $filter) {
            $filters[$i][0] = new Not($filter[0]);
            $filters[$i][1] = 'NOT (' . $filter[1] . ')';
        }

        return $filters;
    }

    /**
     * @dataProvider notDataProvider
     */
    public function testNotFilter(FilterInterface $filter, string $condition): void
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
}
