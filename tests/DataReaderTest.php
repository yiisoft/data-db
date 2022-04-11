<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\TestSupport\Query;

final class DataReaderTest extends TestCase
{
    public function testDataReader(): void
    {
        $query = new Query();
        $all = $query->all();
        $dataReader = new QueryDataReader($query);

        $this->assertSame($query->count(), $dataReader->count());

        foreach ($dataReader as $i => $row) {
            $this->assertSame($all[$i], $row);
        }
    }

    public function testOffset(): void
    {
        $query = new Query();
        $dataReader = (new QueryDataReader($query))->withOffset(2);
        $query->offset(2);

        $this->assertSame(3, $query->one()['id']);
        $this->assertSame($query->one(), $dataReader->readOne());
        $this->assertSame($query->count(), count($dataReader->read()));
    }

    public function testLimit(): void
    {
        $query = new Query();
        $dataReader = (new QueryDataReader($query))->withOffset(1)->withLimit(1);
        $query->offset(1)->limit(1);

        $this->assertSame(2, $query->one()['id']);
        $this->assertSame($query->one(), $dataReader->readOne());
        $this->assertSame($query->count(), count($dataReader->read()));
    }
}
