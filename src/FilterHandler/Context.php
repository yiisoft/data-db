<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Db\ValueNormalizerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class Context
{
    public function __construct(
        private readonly CriteriaHandler $criteriaHandler,
        private readonly ValueNormalizerInterface $valueNormalizer,
    ) {
    }

    public function handleFilter(FilterInterface $filter): ?Condition
    {
        return $this->criteriaHandler->handle($filter);
    }

    public function normalizeValueToScalar(mixed $value): bool|string|int|float
    {
        return $this->valueNormalizer->toScalar($value);
    }
}
