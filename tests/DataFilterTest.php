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
            'equals' => [
                new Equals('equals', 1),
                '[equals] = 1',
            ],
            'between' => [
                new Between('column', 100, 300),
                '[column] BETWEEN 100 AND 300',
            ],
            'greater-than' => [
                new GreaterThan('column', 1000),
                '[column] > 1000',
            ],
            'greater-than-or-equal' => [
                new GreaterThanOrEqual('column', 3.5),
                '[column] >= 3.5',
            ],
            'less-than' => [
                new LessThan('column', 10.7),
                '[column] < 10.7',
            ],
            'less-than-or-equal' => [
                new LessThanOrEqual('column', 100),
                '[column] <= 100',
            ],
            'in' => [
                new In('column', [10, 20, 30]),
                '[column] IN (10, 20, 30)',
            ],
            'like' => [
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
        return [
            'equals' => [
                new Not(new Equals('equals', 1)),
                '[equals] != 1',
            ],
            'between' => [
                new Not(new Between('column', 100, 300)),
                '[column] NOT BETWEEN 100 AND 300',
            ],
            'greater-than' => [
                new Not(new GreaterThan('column', 1000)),
                '[column] <= 1000',
            ],
            'greater-than-or-equal' => [
                new Not(new GreaterThanOrEqual('column', 3.5)),
                '[column] < 3.5',
            ],
            'less-than' => [
                new Not(new LessThan('column', 10.7)),
                '[column] >= 10.7',
            ],
            'less-than-or-equal' => [
                new Not(new LessThanOrEqual('column', 100)),
                '[column] > 100',
            ],
            'in' => [
                new Not(new In('column', [10, 20, 30])),
                '[column] NOT IN (10, 20, 30)',
            ],
            'like' => [
                new Not(new Like('column', 'foo')),
                "[column] NOT LIKE '%foo%'",
            ],
        ];
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
