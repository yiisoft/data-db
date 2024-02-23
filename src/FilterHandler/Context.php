<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Db\ValueNormalizerInterface;

final class Context
{
    public function __construct(
        private readonly CriteriaHandler $criteriaHandler,
        private readonly ValueNormalizerInterface $valueNormalizer,
    ) {
    }

    public function handleCriteria(array $criteria): ?Condition
    {
        return $this->criteriaHandler->handle($criteria);
    }

    public function normalizeValueToScalar(mixed $value): bool|string|int|float
    {
        return $this->valueNormalizer->toScalar($value);
    }

    public function normalizeValueToScalarOrNull(mixed $value): bool|string|null|int|float
    {
        return $this->valueNormalizer->toScalarOrNull($value);
    }
}
