<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use DateTimeInterface;
use RuntimeException;

final class ValueNormalizer implements ValueNormalizerInterface
{
    public function __construct(
        private readonly string $dateTimeFormat = 'Y-m-d H:i:s',
    ) {
    }

    public function toScalar(mixed $value): bool|string|int|float
    {
        if (is_scalar($value)) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format($this->dateTimeFormat);
        }

        throw new RuntimeException('Invalid value.');
    }

    public function toScalarOrNull(mixed $value): bool|string|null|int|float
    {
        if ($value === null) {
            return null;
        }
        return $this->toScalar($value);
    }
}
