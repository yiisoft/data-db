<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler\LikeFilterHandler;

use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\FilterHandlerInterface;

abstract class BaseLikeFilterHandler implements FilterHandlerInterface
{
    protected array $escapingReplacements = [
        '%' => '\%',
        '_' => '\_',
        '\\' => '\\\\',
    ];

    public function getFilterClass(): string
    {
        return Like::class;
    }

    protected function prepareValue(string $value, bool $escape = false): string
    {
        return '%' . strtr($value, $this->escapingReplacements) . '%';
    }
}
