<?php


declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Db\Filter\All as FilterAll;
use Yiisoft\Data\Db\Filter\Equals as FilterEquals;
use Yiisoft\Data\Db\Processor\All as ProcessorAll;
use Yiisoft\Data\Db\Processor\Equals as ProcessorEquals;
use Yiisoft\Data\Db\Processor\QueryProcessorInterface;

class DataReaderFilterTest extends TestCase
{
    public function testInterface()
    {
        $filterEquals = new FilterEquals('test', null);
        $filterAll = new FilterAll($filterEquals);
        $processorAll = new ProcessorAll();
        $processorEquals = new ProcessorEquals();

        $this->assertEquals($filterEquals instanceof FilterInterface, true);
        $this->assertEquals($filterAll instanceof FilterInterface, true);
        $this->assertEquals($processorAll instanceof QueryProcessorInterface, true);
        $this->assertEquals($processorEquals instanceof QueryProcessorInterface, true);
    }

    public function testWithNull()
    {
        $equalsNull = (new FilterEquals('test', null));
        $equalsNotNull = (new FilterEquals('test', 1));
        $all = new FilterAll($equalsNull, $equalsNotNull);

        $this->assertEquals(['IS', 'test', null], $equalsNull->toArray());
        $this->assertEquals(['=', 'test', 1], $equalsNotNull->toArray());
        $this->assertEquals(['and', ['IS', 'test', null], ['=', 'test', 1]], $all->toArray());
    }

    public function testIgnoreNull()
    {
        $equalsNull = (new FilterEquals('test', null))->withIgnoreNull(true);
        $equalsNotNull = (new FilterEquals('test', 1))->withIgnoreNull(true);
        $all = new FilterAll($equalsNull, $equalsNotNull);
        $allOnlyNull = new FilterAll($equalsNull);

        $this->assertEquals([], $equalsNull->toArray());
        $this->assertEquals(['=', 'test', 1], $equalsNotNull->toArray());
        $this->assertEquals(['and', ['=', 'test', 1]], $all->toArray());
        $this->assertEquals([], $allOnlyNull->toArray());
    }
}
