<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

interface ValueNormalizerInterface
{
    public function toScalar(mixed $value): bool|string|int|float;

    public function toScalarOrNull(mixed $value): bool|string|null|int|float;
}
