<?php

namespace Stormmore\Framework\Request;

class Redirect
{
    public ?string $location = null;
    public ?string $body = null;

    public function __construct(string $url)
    {
        $this->location = $url;
    }
}