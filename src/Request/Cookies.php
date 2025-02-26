<?php

namespace Stormmore\Framework\Request;

class Cookies
{
    private array $cookies = [];

    public function __construct()
    {
        foreach($_COOKIE as $name => $value)
        {
            $this->cookies[$name] = new Cookie($name, $value);
        }
    }

    function get(string $name): Cookie
    {
        return $this->cookies[$name];
    }

    function has(string $name): bool
    {
        return array_key_exists($name, $this->cookies);
    }

    function set(Cookie $cookie): void
    {
        $this->cookies[$cookie->getName()] = $cookie->getValue();
        setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpires(), $cookie->getPath());
    }

    function delete(string $name): void
    {
        unset($this->cookies[$name]);
        setcookie($name, '', -1, '/');
    }
}