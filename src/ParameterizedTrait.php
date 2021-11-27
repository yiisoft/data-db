<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

trait ParameterizedTrait
{
    private ?string $paramName = null;

    public function withParamName(?string $name): self
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

        $explode = explode('\\', __CLASS__);

        return $explode[count($explode) - 1];
    }
}
