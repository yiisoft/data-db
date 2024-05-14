<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\CustomerDataReader;
use Yiisoft\Data\Db\Tests\Support\CustomerDTO;
use Yiisoft\Data\Db\Tests\Support\CustomerQuery;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

abstract class DataReaderTest extends TestCase
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

    public static function sortDataProvider(): array
    {
        return [
            [
                Sort::only([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,email'),
                '`name` DESC, `email`',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,-email'),
                '`name` DESC, `email` DESC',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withoutDefaultSorting()
                ->withOrderString('-email'),
                '`email` DESC',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-email'),
                '`email` DESC, `name`',
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
                '`name` DESC NULLS LAST',
            ],
        ];
    }

    #[DataProvider('sortDataProvider')]
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

    public function testCount(): void
    {
        $query = new CustomerQuery($this->getConnection());
        $dataReader = (new CustomerDataReader($query));

        $this->assertEquals($query->count(), $dataReader->count());
    }

    public function testDtoCreateItem(): void
    {
        $query = new CustomerQuery($this->getConnection());
        $dataReader = (new CustomerDataReader($query))
            ->withBatchSize(null);


        $this->assertInstanceOf(CustomerDTO::class, $dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            $this->assertInstanceOf(CustomerDTO::class, $row);
        }
    }

    public function testObjectCreateItem(): void
    {
        $query = (new CustomerQuery($this->getConnection()))
            ->asObject(true);
        $dataReader = (new QueryDataReader($query))
            ->withBatchSize(null);

        $this->assertInstanceOf(stdClass::class, $dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            $this->assertInstanceOf(stdClass::class, $row);
        }
    }

    public function testArrayCreateItem(): void
    {
        $query = new CustomerQuery($this->getConnection());
        $dataReader = (new QueryDataReader($query))
            ->withBatchSize(null);

        $this->assertIsArray($dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            $this->assertIsArray($row);
        }
    }
}
