<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use DateTimeInterface;

trait DateTimeTrait
{
    private ?string $dateTimeFormat = null;

    public function withDateTimeFormat(string $format): self
    {
        $new = clone $this;
        $new->dateTimeFormat = $format;

        return $new;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function dateTimeFormat($value)
    {
        if ($this->dateTimeFormat && $value instanceof DateTimeInterface) {
            return $value->format($this->dateTimeFormat);
        }

        return $value;
    }

    /**
     * @psalm-param array<int, mixed> $values
     *
     * @return array
     */
    private function dateTimeFormatMultiple(...$values): array
    {
        return array_map([$this, 'dateTimeFormat'], $values);
    }
}
