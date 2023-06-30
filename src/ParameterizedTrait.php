<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use ReflectionClass;

trait ParameterizedTrait
{
    private ?string $paramName = null;

    public function withParamName(?string $name): static
    {
        $new = clone $this;
        $new->paramName = $name;

        return $new;
    }

    public function getParamName(): string
    {
        if (!empty($this->paramName)) {
            return $this->paramName;
        }

        return (new ReflectionClass($this))->getShortName();
    }
}
