<?php

namespace Stormmore\Framework\Request;

class Response
{
    public int $code = 200;
    public string $redirect;
    public ?string $location = null;
    public ?string $body = null;
    /**
     * @type string[]
     */
    public array $headers = [];

    public function setCookie($name, $value): void
    {
        Cookies::set($name, $value);
    }

    public function deleteCookie($name): void
    {
        Cookies::delete($name);
    }

    public function setRedirectMessage(string $name, string $message = ''): void
    {
        RedirectMessage::add($name, _($message));
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
}