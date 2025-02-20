<?php

namespace Stormmore\Framework\Request;
class RedirectMessage
{
    private static string $name = 'redirect-msg-';

    public static function isset($name): bool
    {
        $cookieName = self::$name . $name;
        if (Cookies::has($cookieName)) {
            Cookies::delete($cookieName);
            return true;
        }

        return false;
    }

    public static function add(string $name, string $message = ''): void
    {
        Cookies::set(self::$name . $name, $message);
    }

    public static function has($name): bool
    {
        return Cookies::has(self::$name . $name);
    }

    public static function get($name): string
    {
        $message = null;
        $cookieName = self::$name . $name;
        if (Cookies::has($cookieName)) {
            $message = Cookies::get($cookieName);
            Cookies::delete($cookieName);
        }

        return $message;
    }
}