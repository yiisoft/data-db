<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql\Filter;

use InvalidArgumentException;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\ParameterizedTrait;
use Yiisoft\Db\Expression\Expression;

final class TsVector extends CompareFilter
{
    use ParameterizedTrait;

    public const TS_QUERY = 'to_tsquery';
    public const PLAIN_TS_QUERY = 'plainto_tsquery';
    public const PHRASE_TS_QUERY = 'phraseto_tsquery';
    public const WEBSEARCH_TS_QUERY = 'websearch_to_tsquery';

    private const ALLOWED_METHODS = [
        self::TS_QUERY,
        self::PLAIN_TS_QUERY,
        self::PHRASE_TS_QUERY,
        self::WEBSEARCH_TS_QUERY,
    ];

    public static ?string $defaultConfig = null;

    private bool $all = true;
    private bool $startsWith = false;
    private ?string $config = null;
    private string $method = self::TS_QUERY;

    /**
     * @param mixed $column
     * @param mixed $value
     */
    public function __construct($column, $value, ?string $table = null)
    {
        if (!is_array($value) && !is_string($value) && $value !== null) {
            $type = \is_object($value) ? \get_class($value) : \gettype($value);
            throw new InvalidArgumentException('Value must be type of array, string or null. "' . $type . '" given.');
        }

        parent::__construct($column, $value, $table);
    }

    public static function getOperator(): string
    {
        return 'pgsql.ts_vector';
    }

    public function all(): self
    {
        if ($this->all === true) {
            return $this;
        }

        $new = clone $this;
        $new->all = true;

        return $new;
    }

    public function any(): self
    {
        if ($this->all === false) {
            return $this;
        }

        $new = clone $this;
        $new->all = false;

        return $new;
    }

    public function startsWith(bool $value): self
    {
        if ($this->startsWith === $value) {
            return $this;
        }

        $new = clone $this;
        $new->startsWith = $value;

        return $new;
    }

    public function withConfig(?string $config): self
    {
        $new = clone $this;
        $new->config = $config;

        return $new;
    }

    public function withMethod(string $method): self
    {
        if (!in_array($method, self::ALLOWED_METHODS, true)) {
            throw new InvalidArgumentException('Method must be one of ' . implode(', ', self::ALLOWED_METHODS) . '. "' . $method . '" given');
        }

        $new = clone $this;
        $new->method = $method;

        return $this;
    }

    public function toArray(): array
    {
        if ($this->value === null) {
            return parent::toArray();
        }

        $value = $this->value;
        $paramName = $this->getParamName();
        $config = $this->config ?? self::$defaultConfig;

        if (is_array($value)) {
            if ($this->startsWith && $this->all) {
                end($value);
                $key = key($value);
                $value[$key] .= ':*';
            } elseif ($this->startsWith) {
                $value = array_map(fn ($val) => $val . ':*', $value);
            }

            $separator = $this->all ? ' & ' : ' | ';
            $value = implode($separator, $value);
        } elseif ($this->startsWith) {
            $value .= ':*';
        }

        $params = [
            ':' . $paramName => $value
        ];

        $expression = $this->method . '(';

        if ($config) {
            $expression .= ':' . $paramName . '_config,';
            $params[':' . $paramName . '_config'] = $config;
        }

        $expression .= ':' . $paramName . ')';

        return ['@@', $this->column, new Expression($expression, $params)];
    }
}
