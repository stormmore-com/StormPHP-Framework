<?php

namespace Stormmore\Framework\Mvc\IO\Cookie;

class Cookies
{
    private array $setCookies = [];
    private array $unsetCookies = [];
    private array $cookies = [];

    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    function get(string $name): Cookie
    {
        return $this->cookies[$name];
    }

    function has(string $name): bool
    {
        return array_key_exists($name, $this->cookies);
    }

    function getSetCookies(): array
    {
        return $this->setCookies;
    }

    function getUnsetCookies(): array
    {
        return $this->unsetCookies;
    }

    public function setCookie(Cookie $cookie): void
    {
        $this->setCookies[] = $cookie;
    }

    public function unsetCookie(string $name): void
    {
        $this->unsetCookies[] = $name;
    }
}