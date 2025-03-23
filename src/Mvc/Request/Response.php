<?php

namespace Stormmore\Framework\Mvc\Request;

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

    public RedirectMessage $messages;

    public function __construct(public Cookies $cookies)
    {
        $this->messages = new RedirectMessage($cookies);
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
}