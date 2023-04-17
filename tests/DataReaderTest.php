<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Db\Query\Query;

final class DataReaderTest extends TestCase
{
    use TestTrait;

    public function testDataReader(): void
    {
        $db = $this->getConnection(true);

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
        $db = $this->getConnection(true);

        $query = (new Query($db))
            ->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withOffset(2);
        $query->offset(2);

        $actual = $dataReader->getPreparedQuery()->createCommand()->getRawSql();
        $expected = $query->createCommand()->getRawSql();

        $this->assertSame($expected, $actual);
        $this->assertStringContainsStringIgnoringCase('OFFSET 2', $actual);
    }

    public function testLimit(): void
    {
        $db = $this->getConnection(true);

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
        $this->assertStringContainsStringIgnoringCase('LIMIT 1', $actual);
        $this->assertStringContainsStringIgnoringCase('OFFSET 1', $actual);
    }
}
