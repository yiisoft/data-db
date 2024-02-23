<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

trait Params
{
    private array $params = [];

    public function withParams(array $params): static
    {
        $new = clone $this;
        $new->params = $params;
        return $new;
    }

    public function withParam(string $name, mixed $value): static
    {
        $new = clone $this;
        $new->params[$name] = $value;
        return $new;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
