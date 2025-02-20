<?php

namespace Stormmore\Framework\Authentication;

use Attribute;

#[Attribute]
class Authorize
{
    public array $claims = array();

    public function __construct(string ...$claims)
    {
        $this->claims = $claims;
    }
}