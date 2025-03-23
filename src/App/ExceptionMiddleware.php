<?php

namespace Stormmore\Framework\App;

use closure;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Logger\ILogger;
use Stormmore\Framework\Mvc\Authentication\AjaxAuthenticationException;
use Stormmore\Framework\Mvc\Authentication\AuthenticationException;
use Stormmore\Framework\Mvc\Authentication\AuthorizedException;
use Stormmore\Framework\Mvc\Request\Request;
use Stormmore\Framework\Mvc\Request\Response;
use Throwable;

readonly class ExceptionMiddleware implements IMiddleware
{
    public function __construct(
        private AppConfiguration $configuration,
        private ILogger          $logger,
        private Request          $request,
        private Response         $response)
    {
    }

    public function run(closure $next): void
    {
        $this->logger->logI("Request started  `{$this->request->uri}`");
        try {
            $next();
        } catch (Throwable $throwable) {
            $this->handle($throwable);
        }
        $this->logger->logI("Request finished `{$this->request->uri}` [{$this->response->code}]");
    }

    private function handle(Throwable $throwable): void
    {
        $errors = $this->configuration->errors;

        if ($throwable instanceof AjaxAuthenticationException) {
            $this->response->code = 401;
            return;
        }
        if ($throwable instanceof AuthenticationException and array_key_exists('unauthenticated', $errors)) {
            $redirectFrom = $this->request->encodeRequestUri();
            $redirect = $errors['unauthenticated'];
            $location = $redirect->location . '?from=' . $redirectFrom;
            $this->response->location = $location;
            return;
        }
        if ($throwable instanceof AuthorizedException and array_key_exists('unauthorized', $errors)) {
            $redirect = $errors['unauthorized'];
            $this->response->location = $redirect->location;
            return;
        }

        $code = (!is_int($throwable->getCode()) or $throwable->getCode() == 0) ? 500 : $throwable->getCode();
        $this->response->code = $code;

        if (array_key_exists($code, $errors) and is_string($errors[$code])) {
            $this->response->body = $this->getErrorPageContent(resolve_path_alias($errors[$code]), $throwable);
        } else if (array_key_exists('default', $errors)) {
            $this->response->body = $this->getErrorPageContent(resolve_path_alias($errors['default']), $throwable);
        } else {
            $this->printException($throwable);
        }
    }

    private function getErrorPageContent(string $path, Throwable $throwable): string
    {
        ob_start();
        include_once resolve_path_alias($path);
        return ob_get_clean();
    }

    private function printException(Throwable $throwable): void
    {
        $this->response->body = "";
        if ($this->configuration->isDevelopment()) {
            $this->response->body .= "<h2>{$throwable->getMessage()}</h2>";
            $this->response->body .= "<pre>";
            foreach ($throwable->getTrace() as $k => $trace) {
                $this->response->body .= "#$k " . $trace['file'] . ':' . $trace['line'] . '</br>';
            }
            $this->response->body .= "</pre>";
        } else {
            $this->response->body .= "
                  <h1>Ooooops. Something went wrong</h1>
                  <p>Something is broken.</p>
                ";
        }
    }
}