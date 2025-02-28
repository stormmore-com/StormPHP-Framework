<?php

namespace Stormmore\Framework\App;

use closure;
use Stormmore\Framework\Request\Response;

readonly class ResponseMiddleware implements IMiddleware
{
    public function __construct(private Response $response)
    {
    }

    public function run(closure $next): void
    {
        ob_start();
        $next();
        ob_clean();

        if ($this->response->location) {
            header("Location: {$this->response->location}");
            die;
        }
        http_response_code($this->response->code);
        foreach ($this->response->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->response->body;
    }
}