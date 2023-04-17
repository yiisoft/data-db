<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Support;

use Psr\SimpleCache\CacheInterface;
use function array_key_exists;

final class Cache implements CacheInterface
{
    private static array $cache = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return self::$cache[$key] ?? $default;
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null): bool
    {
        self::$cache[$key] = $value;

        return $this->has($key);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, self::$cache);
    }

    public function delete(string $key): bool
    {
        unset(self::$cache[$key]);

        return $this->has($key) === false;
    }

    public function clear(): bool
    {
        self::$cache = [];

        return self::$cache === [];
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key, $default);
        }
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null): bool
    {
        $result = false;

        foreach ($values as $key => $value) {
            $result = $this->set($key, $value, $ttl);
        }

        return $result;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $result = false;

        foreach ($keys as $key) {
            $result = $this->delete($key);
        }

        return $result;
    }
}
