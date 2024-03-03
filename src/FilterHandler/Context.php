<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\FilterHandler;
use Yiisoft\Data\Db\ValueNormalizerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class Context
{
    public function __construct(
        private readonly FilterHandler $filterHandler,
        private readonly ValueNormalizerInterface $valueNormalizer,
    ) {
    }

    public function handleFilter(FilterInterface $filter): ?Criteria
    {
        return $this->filterHandler->handle($filter);
    }

    public function normalizeValueToScalar(mixed $value): bool|string|int|float
    {
        return $this->valueNormalizer->toScalar($value);
    }
}
