<?php

namespace Stormmore\Framework\Mvc\IO;

use Stormmore\Framework\Mvc\IO\Cookie\SetCookie;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;

class RedirectMessage
{
    private string $prefix = 'redirect-msg-';

    public function __construct(private readonly Cookies $cookies)
    {
    }

    public function isset($name): bool
    {
        $cookieName = $this->prefix . $name;
        if ($this->cookies->has($cookieName)) {
            $this->cookies->unsetCookie($cookieName);
            return true;
        }

        return false;
    }

    public function has($name): bool
    {
        return $this->cookies->has($this->prefix . $name);
    }

    public function add(string $name, string $message = '1'): void
    {
        $this->cookies->setCookie(new SetCookie($this->prefix . $name, $message));
    }

    public function get($name): string
    {
        $message = null;
        $cookieName = $this->prefix . $name;
        if ($this->cookies->has($cookieName)) {
            $message = $this->cookies->get($cookieName)->getValue();
            $this->cookies->unsetCookie($cookieName);
        }

        return $message;
    }
}