<?php

namespace Stormmore\Framework\App;

use Stormmore\Framework\Mvc\View;
use Stormmore\Framework\Request\Redirect;
use Stormmore\Framework\Request\Response;

class ResponseHandler
{
    public function handle(Response $response, mixed $result): void
    {
        if ($result instanceof View) {
            $response->body = $result->toHtml();
        } else if ($result instanceof Redirect) {
            $response->location = $result->location;
        } else if (is_object($result) or is_array($result)) {
            $response->addHeader("Content-Type", "application/json; charset=utf-8");
            $response->body = json_encode($result);
        } else if (is_string($result) || is_numeric($result)) {
            $response->body = $result;
        }
        if ($response->location) {
            header("Location: $response->location");
            die;
        }
        http_response_code($response->code);
        foreach ($response->headers as $name => $value) {
            header("$name: $value");
        }
        echo $response->body;
    }
}