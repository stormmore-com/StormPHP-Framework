<?php

namespace Stormmore\Framework\Request;

use Stormmore\Framework\App;

class Redirect
{
    public ?string $location = null;
    public ?string $body = null;

    public function __construct(string $url)
    {
        $baseUrl = App::getInstance()->getAppConfiguration()->baseUrl;
        if (str_starts_with($url, "http")) {
            $this->location = $url;
        } else if ($baseUrl != null and str_starts_with($baseUrl, 'http')) {
            $this->location = $baseUrl . $url;
        }
    }
}