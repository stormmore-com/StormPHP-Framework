<?php

namespace Stormmore\Framework\Http;

use Exception;

class HttpClientException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}