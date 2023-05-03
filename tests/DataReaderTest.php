<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

final class DataReaderTest extends TestCase
{
    use TestTrait;

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

    public function testOffset(): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withOffset(2);
        $query->offset(2);

        $actual = $dataReader->getPreparedQuery()->createCommand()->getRawSql();
        $expected = $query->createCommand()->getRawSql();

        $this->assertSame($expected, $actual);
        $this->assertStringEndsWith('OFFSET 2', $actual);
    }

    public function testLimit(): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withOffset(1)
            ->withLimit(1);
        $query
            ->offset(1)
            ->limit(1);

        $actual = $dataReader->getPreparedQuery()->createCommand()->getRawSql();
        $expected = $query->createCommand()->getRawSql();

        $this->assertSame($expected, $actual);
        $this->assertStringEndsWith('LIMIT 1 OFFSET 1', $actual);
    }

    public function sortDataProvider(): array
    {
        return [
            [
                Sort::only([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,email'),
                '[name] DESC, [email]',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,-email'),
                '[name] DESC, [email] DESC',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withoutDefaultSorting()
                ->withOrderString('-email'),
                '[email] DESC',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-email'),
                '[email] DESC, [name]',
            ],
            [
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
                '[name] DESC NULLS LAST',
            ],
        ];
    }

    /**
     * @dataProvider sortDataProvider
     */
    public function testSort(Sort $sort, string $expected): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withSort($sort);

        $this->assertStringEndsWith(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql()
        );
    }
}
