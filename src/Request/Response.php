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

    public function __construct(public Cookies $cookies)
    {
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