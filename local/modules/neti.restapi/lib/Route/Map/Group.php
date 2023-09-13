<?php

namespace Neti\RestApi\Route\Map;

class Group
{
    private static array $groupStorage = [];
    private static array $storage = [];

    public static function set(array $args): array
    {
        return self::$groupStorage[] = [...$args];
    }

    public static function get(): array
    {
        if (empty(self::$groupStorage)) return self::$groupStorage;

        $url = '';
        $middlewares = [];
        foreach (self::$groupStorage as $group) {
            if (isset($group['prefix'])) {
                $url .= $group['prefix'];
            }
            if (isset($group['middleware'])) {
                if (is_array($group['middleware'])) {
                    $middlewares = array_merge($middlewares, $group['middleware']);
                } else {
                    $middlewares[] = $group['middleware'];
                }
            }
        }

        self::$storage[] = array_filter([
            'prefix' => $url,
            'middleware' => $middlewares
        ]);

        return self::$storage;
    }

    public static function remove(): array
    {
        $lastKey = array_key_last(self::$groupStorage);
        unset(self::$groupStorage[$lastKey]);

        $lastKey = array_key_last(self::$storage);
        unset(self::$storage[$lastKey]);

        return self::get();
    }
}