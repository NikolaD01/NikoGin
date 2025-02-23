<?php

namespace NikoGin\Core\Support;

use Exception;

class Container
{
    private static array $instances = [];

    public static function bind(string $key, callable $resolver): void
    {
        self::$instances[$key] = $resolver;
    }

    /**
     * @throws Exception
     */
    public static function get(string $key) {
        if (isset(self::$instances[$key])) {
            return call_user_func(self::$instances[$key]);
        }
        throw new Exception("Service not found: {$key}");
    }
}