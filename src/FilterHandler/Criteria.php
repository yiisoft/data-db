<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

final class Criteria
{
    public function __construct(
        public readonly array $condition,
        public readonly array $params = [],
    ) {
    }
}
