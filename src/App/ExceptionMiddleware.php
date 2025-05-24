<?php

namespace Stormmore\Framework\App;

use closure;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Logger\ILogger;
use Stormmore\Framework\Mvc\Authentication\AjaxAuthenticationException;
use Stormmore\Framework\Mvc\Authentication\AuthenticationException;
use Stormmore\Framework\Mvc\Authentication\AuthorizedException;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Mvc\IO\Response;
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

    public function run(closure $next, array $options = []): void
    {
        $this->logger->logI("Request started  `{$this->request->path}`");
        try {
            $next();
            $this->logger->logI("Request finished `{$this->request->path}` [{$this->response->code}]");
        } catch (Throwable $throwable) {
            $this->handle($throwable);
            $this->logger->logE("Request failed  `{$this->request->path}` [{$this->response->code}]", $throwable);
        }
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

        if ($this->configuration->isDevelopment() and $code == 500) {
            $this->printException($throwable);
        } else if (array_key_exists($code, $errors)) {
            $this->response->body = $this->getErrorPageContent(resolve_path_alias($errors[$code]), $throwable);
        } else {
            $this->printException($throwable);
        }
    }

    private function getErrorPageContent(string $path, Throwable $throwable): string
    {
        ob_start();
        try {
            include_once resolve_path_alias($path);
            return ob_get_clean();
        }
        catch(Throwable $t) {
            ob_clean();
            throw $t;
        }
    }

    private function printException(Throwable $throwable): void
    {
        $this->response->body = "";
        if ($this->configuration->isDevelopment()) {
            $this->response->body .= "<h2>Exception: {$throwable->getMessage()}</h2>";

            $this->response->body .= "<h3>#0 " . $throwable->getFile() . ': ' . $throwable->getLine() . "</h3>\n";
            $this->response->body .= "<pre>";

            foreach ($throwable->getTrace() as $k => $trace) {
                if (array_key_exists('file', $trace) and array_key_exists('line', $trace)) {
                    $idx = $k + 1;
                    $this->response->body .= "#$idx " . $trace['file'] . ': ' . $trace['line'] . '</br>';
                }
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