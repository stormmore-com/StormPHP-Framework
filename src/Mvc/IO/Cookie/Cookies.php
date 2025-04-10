<?php

namespace Stormmore\Framework\Mvc\IO\Cookie;

class Cookies
{
    private array $cookies = [];

    public function __construct()
    {
    }

    function getAll(): array
    {
        return $this->cookies;
    }

    function get(string $name): Cookie
    {
        return $this->cookies[$name];
    }

    function has(string $name): bool
    {
        return array_key_exists($name, $this->cookies);
    }

    public function add(Cookie $cookie): void
    {
        $this->cookies[$cookie->getName()] = $cookie;
        setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpires(), $cookie->getPath());

    }

    function delete(string $name): void
    {
        unset($this->cookies[$name]);
        setcookie($name, '', -1, '/');
    }
}