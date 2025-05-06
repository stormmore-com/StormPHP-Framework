<?php

namespace Stormmore\Framework\App;

use closure;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Mvc\IO\Response;

readonly class ResponseMiddleware implements IMiddleware
{
    public function __construct(private Response $response, private RequestContext $requestContext, private AppConfiguration $configuration)
    {
    }

    public function run(closure $next): void
    {
        try {
           if ($this->configuration->isProduction()) ob_start();
            $next();
        } finally {
           if ($this->configuration->isProduction()) ob_end_flush();
        }

        if ($this->requestContext->isCliRequest()) {
            echo "<http-header>Status-Code: {$this->response->code}</http-header>\n";
            foreach($this->response->headers as $name => $value) {
                echo "<http-header>$name: $value</http-header>\n";
            }
            foreach($this->response->getCookies()->getSetCookies() as $cookie) {
                echo "<http-header>Set-Cookie: {$cookie->getName()}={$cookie->getValue()}</http-header>\n";
            }
        }
        else {
            foreach($this->response->getCookies()->getSetCookies() as $cookie) {
                setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpires(), $cookie->getPath());
            }
            foreach($this->response->getCookies()->getUnsetCookies() as $cookieName) {
                setcookie($cookieName, '', -1, '/');
            }
            if ($this->response->location) {
                header("Location: {$this->response->location}");
                die;
            }
            http_response_code($this->response->code);
            foreach ($this->response->headers as $name => $value) {
                header("$name: $value");
            }
        }
        echo $this->response->body;
    }
}