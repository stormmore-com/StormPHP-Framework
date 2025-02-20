<?php

namespace Stormmore\Framework\Controller;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public array $urls = array();

    public function __construct(string ...$url)
    {
        $this->urls = array($url);
    }
}