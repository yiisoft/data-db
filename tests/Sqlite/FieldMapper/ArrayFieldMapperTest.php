<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FieldMapper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\FieldMapper\ArrayFieldMapper;
use Yiisoft\Db\Expression\Expression;

final class ArrayFieldMapperTest extends TestCase
{
    public function testBase(): void
    {
        $expression = new Expression("data->>'profile'");
        $mapper = new ArrayFieldMapper([
            'name' => 'username',
            'jobId' => 'job_id',
            'profileData' => $expression,
        ]);

        $this->assertSame('id', $mapper->map('id'));
        $this->assertSame('username', $mapper->map('name'));
        $this->assertSame('job_id', $mapper->map('jobId'));
        $this->assertSame($expression, $mapper->map('profileData'));
    }
}
