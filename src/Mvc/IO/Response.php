<?php

namespace Stormmore\Framework\Mvc\IO;

use Stormmore\Framework\Mvc\IO\Cookie\Cookies;

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

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setJson(array|object|string $json): void
    {
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        if (!is_string($json)) {
            $json = json_encode($json);
        }
        $this->body = $json;
    }
}