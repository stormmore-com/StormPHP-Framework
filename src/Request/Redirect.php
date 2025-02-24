<?php

namespace Stormmore\Framework\Request;

use Stormmore\Framework\App;

class Redirect
{
    public ?string $location = null;
    public ?string $body = null;

    public function __construct(string $url)
    {
        $this->location = $url;
    }
}