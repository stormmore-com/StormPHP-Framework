<?php

namespace Stormmore\Framework\Mvc\IO\Cookie;

use Stormmore\Framework\Http\Interfaces\ICookie;

class Cookies
{
    private array $setCookies = [];
    private array $unsetCookies = [];
    private array $cookies = [];

    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    function get(string $name): ICookie
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

    public function setCookie(SetCookie $cookie): void
    {
        $this->setCookies[] = $cookie;
    }

    public function unsetCookie(string $name): void
    {
        $this->unsetCookies[] = $name;
    }
}