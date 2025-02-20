<?php

namespace Stormmore\Framework\Request;

class Cookies
{
    static function get(string $name): string
    {
        return $_COOKIE[$name];
    }

    static function has(string $name): bool
    {
        return array_key_exists($name, $_COOKIE);
    }

    static function set(string $name, string $value, int $expires = 0): void
    {
        $_COOKIE[$name] = $value;
        setcookie($name, $value, $expires, '/');
    }

    static function delete(string $name): void
    {
        unset($_COOKIE[$name]);
        setcookie($name, '', -1, '/');
    }
}