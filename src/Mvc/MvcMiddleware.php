<?php

namespace Stormmore\Framework\Mvc;

use closure;
use Exception;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Mvc\IO\Redirect;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\Mvc\Route\ExecutionRoute;
use Stormmore\Framework\Mvc\Route\Router;
use Stormmore\Framework\Mvc\View\View;
use Stormmore\Framework\SourceCode\SourceCode;
use Stormmore\Framework\Std\Path;
use Throwable;

readonly class MvcMiddleware implements IMiddleware
{
    public function __construct(
        private SourceCode       $sourceCode,
        private AppConfiguration $configuration,
        private Request          $request,
        private Response         $response,
        private Container        $di,
        private Router           $router,
        private Resolver         $diResolver)
    {
    }

    public function run(closure $next, array $options = []): void
    {
        $route = $this->find();
        $route or throw new Exception("APP: route for [{$this->request->path}] doesn't exist", 404);
        $this->request->addRouteParameters($route->parameters);

        $result = $this->handle($route);
        $this->handleResult($result);
    }

    private function handle(ExecutionRoute $route): mixed
    {
        $endpoint = $route->endpoint;
        if ($endpoint->isCallable()) {
            $callable = $this->diResolver->resolve($endpoint->getCallable());
            return $callable();
        }
        if ($endpoint->isController()) {
            $controllerReflection = new ControllerReflection(
                $this->request,
                $this->di, $this->diResolver,
                $endpoint->getControllerActionList());
            $controllerReflection->validate();
            return $controllerReflection->invoke();
        }
        if ($endpoint->isScript()) {
            return $this->getScriptContent($endpoint->getScriptPath());
        }

        $this->request->files->delete();

        return null;
    }

    private function getScriptContent(string $filename): string
    {
        ob_start();
        try {
            $request = $this->request;
            require Path::resolve_alias($filename);
            return ob_get_clean();
        } catch(Throwable $t) {
            ob_clean();
            throw $t;
        }
    }


    private function handleResult(mixed $result): void
    {
        if ($result instanceof View) {
            $this->response->body = $result->toHtml();
        } else if ($result instanceof Redirect) {
            $this->response->location = $result->location;
        } else if (is_object($result) or is_array($result)) {
            $this->response->addHeader("Content-Type", "application/json; charset=utf-8");
            $this->response->body = json_encode($result);
        } else if (is_string($result) || is_numeric($result)) {
            $this->response->body = $result;
        }
    }

    private function find(): ?ExecutionRoute
    {
        $route = $this->router->find($this->request);
        if (!$this->exist($route) and $this->configuration->isDevelopment()) {
            $this->sourceCode->scan();
            $route = $this->router->find($this->request);
            if ($this->exist($route)) {
                $this->sourceCode->writeCache();
                return $route;
            }

            return null;
        }
        return $route;
    }

    private function exist(?ExecutionRoute $route): bool
    {
        if ($route == null)
            return false;
        if ($route->endpoint->isController() and !$route->endpoint->hasControllerReflection()) {
            return false;
        }
        return true;
    }
}