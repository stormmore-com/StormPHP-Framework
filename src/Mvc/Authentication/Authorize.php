<?php

namespace Stormmore\Framework\Mvc\Authentication;

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