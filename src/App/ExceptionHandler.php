<?php

namespace Stormmore\Framework\App;

use closure;
use Exception;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Authentication\AjaxAuthenticationException;
use Stormmore\Framework\Authentication\AuthenticationException;
use Stormmore\Framework\Authentication\AuthorizedException;
use Stormmore\Framework\Request\Request;
use Throwable;


readonly class ExceptionHandler
{
    public function __construct(
        private AppConfiguration $configuration,
        private Request          $request,
        private closure|null     $failCallback
    )
    {
    }

    #[NoReturn]
    public function handle(Throwable $throwable): void
    {
        ob_clean();
        try {
            run_callable($this->failCallback);
        } catch (Exception $innerException) {
            $prev = new Exception($innerException->getMessage(), $innerException->getCode(), $throwable);
            $throwable = new Exception("OnFailure callback failed", 5, $prev);
        }

        $errors = $this->configuration->errors;

        if ($throwable instanceof AjaxAuthenticationException) {
            http_response_code(401);
            die;
        }
        if ($throwable instanceof AuthenticationException and array_key_exists('unauthenticated', $errors)) {
            $redirectFrom = $this->request->encodeRequestUri();
            $redirect = $errors['unauthenticated'];
            $location = $redirect->location . '?from=' . $redirectFrom;
            header("Location: $location");
            die;
        }
        if ($throwable instanceof AuthorizedException and array_key_exists('unauthorized', $errors)) {
            $redirect = $errors['unauthorized'];
            header("Location: {$redirect->location}");
            die;
        }

        $code = (!is_int($throwable->getCode()) or $throwable->getCode() == 0) ? 500 : $throwable->getCode();
        http_response_code($code);

        if (array_key_exists($code, $errors) and is_string($errors[$code])) {
            include_once resolve_path_alias($errors[$code]);
        } else if (array_key_exists('default', $errors)) {
            include_once resolve_path_alias($errors['default']);
        } else {
            $this->printException($throwable);
        }
    }

    private function printException(Throwable $throwable): void
    {
        if ($this->configuration->isDevelopment()) {
            echo "<h2>{$throwable->getMessage()}</h2>";
            echo "<pre>";
            foreach ($throwable->getTrace() as $k => $trace) {
                echo "#$k " . $trace['file'] . ':' . $trace['line'] . '</br>';
            }
            echo "</pre>";
        } else {
            echo "
                  <h1>Ooooops. Something went wrong</h1>
                  <p>Something is broken.</p>
                ";
        }
    }
}