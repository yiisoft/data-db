<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\QueryDataReader;

use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Base\BaseQueryDataReaderTestCase;
use Yiisoft\Data\Db\Tests\Sqlite\DatabaseTrait;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Query\Query;
use Yiisoft\Test\Support\Log\SimpleLogger;

final class QueryDataReaderTest extends BaseQueryDataReaderTestCase
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'SELECT * FROM "customer" LIMIT 9223372036854775807 OFFSET 2',
            ],
        ];
    }

    public static function dataFilterSql(): array
    {
        $data = parent::dataFilterSql();
        $replacementMap = [
            'like' => "[[column]] LIKE '%foo%' ESCAPE '\'",
            'not like' => "[[column]] NOT LIKE '%foo%' ESCAPE '\'",
            'and, or' => '([[null_column]] IS NULL) AND '
                . '([[equals]] = 10) AND '
                . '([[between]] BETWEEN 10 AND 20) AND '
                . "(([[id]] = 8) OR ([[name]] LIKE '%foo%' ESCAPE '\'))",
            'or, and' => '([[greater_than]] > 15) OR '
                . '([[less_than_or_equal]] <= 10) OR '
                . "([[not_equals]] <> 'test') OR "
                . "(([[id]] = 8) AND ([[name]] LIKE '%bar%' ESCAPE '\'))",
            'and, or 2' => "([[id]] > 88) AND (([[state]] = 2) OR ([[name]] LIKE '%eva%' ESCAPE '\'))",
            'or, and 2' => "([[id]] > 88) OR (([[state]] = 2) AND ([[name]] LIKE '%eva%' ESCAPE '\'))",
            'or, or' => "([[id]] > 88) OR (([[state]] = 2) OR ([[name]] LIKE '%eva%' ESCAPE '\'))",
            'and, and' => "([[id]] > 88) AND (([[state]] = 2) AND ([[name]] LIKE '%eva%' ESCAPE '\'))",
        ];
        foreach ($replacementMap as $key => $value) {
            $data[$key][1] = $value;
        }

        return $data;
    }

    public function testLimitOnReadOne(): void
    {
        $logger = new SimpleLogger();
        $db = $this->makeConnection();
        $db->setLogger($logger);
        $query = (new Query($db))->from('customer');
        $dataReader = new QueryDataReader($query);

        $dataReader->readOne();

        $messages = $logger->getMessages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString(' LIMIT 1', $messages[0]['message']);
    }

    public function testBatchSizeOne(): void
    {
        $query = (new Query($this->getConnection()))->from('customer');
        $dataReader = (new QueryDataReader($query))->withBatchSize(1);

        $items = iterator_to_array($dataReader->read());

        $this->assertCount(3, $items);
    }

    public function testLimitZero(): void
    {
        $query = (new Query($this->getConnection()))->from('customer');
        $dataReader = (new QueryDataReader($query))->withLimit(0);

        $this->assertSame(3, $dataReader->count());
        $this->assertCount(0, iterator_to_array($dataReader->read()));
        $this->assertNull($dataReader->readOne());
    }

    public function testCountQuery(): void
    {
        $logger = new SimpleLogger();
        $db = $this->makeConnection();
        $db->setLogger($logger);
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withLimit(2)
            ->withOffset(1)
            ->withSort(Sort::any()->withOrderString('id'));

        $dataReader->count();

        $messages = $logger->getMessages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString(
            'SELECT COUNT(*) FROM (SELECT * FROM "customer") "c"',
            $messages[0]['message'],
        );
    }
}
