<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

final class QueryDataReaderTest extends \Yiisoft\Data\Db\Tests\Base\QueryDataReaderTest
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'SELECT * FROM `customer` LIMIT 9223372036854775807 OFFSET 2',
            ],
        ];
    }

    public static function dataFilterSql(): array
    {
        $data = parent::dataFilterSql();
        $replacementMap = [
            'like' => "[[column]] LIKE '%foo%' ESCAPE '\'",
            'not like' => "[[column]] NOT LIKE '%foo%' ESCAPE '\'",
            'all, any' => '([[null_column]] IS NULL) AND ' .
                '([[equals]] = 10) AND ' .
                '([[between]] BETWEEN 10 AND 20) AND ' .
                "(([[id]] = 8) OR ([[name]] LIKE '%foo%' ESCAPE '\'))",
            'any, all' => '([[greater_than]] > 15) OR ' .
                '([[less_than_or_equal]] <= 10) OR ' .
                "([[not_equals]] != 'test') OR " .
                "(([[id]] = 8) AND ([[name]] LIKE '%bar%' ESCAPE '\'))",
            'all, any 2' => "([[id]] > 88) AND (([[state]] = 2) OR ([[name]] LIKE '%eva%' ESCAPE '\'))",
            'any, all 2' => "([[id]] > 88) OR (([[state]] = 2) AND ([[name]] LIKE '%eva%' ESCAPE '\'))",
            'any, any' => "([[id]] > 88) OR (([[state]] = 2) OR ([[name]] LIKE '%eva%' ESCAPE '\'))",
            'all, all' => "([[id]] > 88) AND (([[state]] = 2) AND ([[name]] LIKE '%eva%' ESCAPE '\'))",
        ];
        foreach ($replacementMap as $key => $value) {
            $data[$key][1] = $value;
        }

        return $data;
    }
}
