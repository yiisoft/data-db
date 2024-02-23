<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

final class Condition
{
    public function __construct(
        public readonly array $body,
        public readonly array $params = [],
    ) {
    }
}
