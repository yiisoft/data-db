<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle;

use Yiisoft\Data\Db\Tests\Base\BaseQueryDataReaderTestCase;

final class QueryDataReaderTest extends BaseQueryDataReaderTestCase
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'WITH USER_SQL AS (SELECT * FROM "customer"), ' .
                'PAGINATION AS (SELECT USER_SQL.*, rownum as rowNumId FROM USER_SQL)' . "\n" .
                'SELECT * FROM PAGINATION WHERE rowNumId > 2',
            ],
        ];
    }

    public static function dataLimit(): array
    {
        return [
            [
                'WITH USER_SQL AS (SELECT * FROM "customer"), ' .
                'PAGINATION AS (SELECT USER_SQL.*, rownum as rowNumId FROM USER_SQL)' . "\n" .
                'SELECT * FROM PAGINATION WHERE rowNumId > 1 AND rownum <= 1',
            ],
        ];
    }

    public static function dataFilterSql(): array
    {
        $data = parent::dataFilterSql();
        $replacementMap = [
            'like' => "[[column]] LIKE '%foo%' ESCAPE '!'",
            'not like' => "[[column]] NOT LIKE '%foo%' ESCAPE '!'",
            'and, or' => '([[null_column]] IS NULL) AND ' .
                '([[equals]] = 10) AND ' .
                '([[between]] BETWEEN 10 AND 20) AND ' .
                "(([[id]] = 8) OR ([[name]] LIKE '%foo%' ESCAPE '!'))",
            'or, and' => '([[greater_than]] > 15) OR ' .
                '([[less_than_or_equal]] <= 10) OR ' .
                "([[not_equals]] != 'test') OR " .
                "(([[id]] = 8) AND ([[name]] LIKE '%bar%' ESCAPE '!'))",
            'and, or 2' => "([[id]] > 88) AND (([[state]] = 2) OR ([[name]] LIKE '%eva%' ESCAPE '!'))",
            'or, and 2' => "([[id]] > 88) OR (([[state]] = 2) AND ([[name]] LIKE '%eva%' ESCAPE '!'))",
            'or, or' => "([[id]] > 88) OR (([[state]] = 2) OR ([[name]] LIKE '%eva%' ESCAPE '!'))",
            'and, and' => "([[id]] > 88) AND (([[state]] = 2) AND ([[name]] LIKE '%eva%' ESCAPE '!'))",
        ];
        foreach ($replacementMap as $key => $value) {
            $data[$key][1] = $value;
        }

        return $data;
    }
}
