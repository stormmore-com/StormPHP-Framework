<?php

namespace Stormmore\Framework\App;

use closure;
use Exception;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Classes\SourceCode;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Mvc\ControllerReflection;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Route\ExecutionRoute;
use Stormmore\Framework\Route\Router;

readonly class RequestHandler
{
    public function __construct(
        private SourceCode       $appCode,
        private AppConfiguration $configuration,
        private Request          $request,
        private Container        $di,
        private Resolver         $diResolver,
        private closure|null     $beforeRunCallback,
        private closure|null     $afterRunCallback)
    {
    }

    public function handle(Router $routes): mixed
    {
        $route = $this->find($routes);
        $route or throw new Exception("APP: route for [{$this->request->uri}] doesn't exist", 404);
        $this->request->addRouteParameters($route->parameters);

        $result = run_callable($this->beforeRunCallback);
        if ($result == null) {
            $result = $this->run($route);
        }
        run_callable($this->afterRunCallback);

        return $result;
    }

    private function find(Router $router): ?ExecutionRoute
    {
        $route = $router->find($this->request);
        if (!$this->exist($route) and $this->configuration->isDevelopment()) {
            $this->appCode->scanFiles();
            $this->appCode->scanRoutes();
            $router->addRoutes($this->appCode->routes);
            $route = $router->find($this->request);
            if ($this->exist($route)) {
                $this->appCode->writeClassCache();
                $this->appCode->writeRouteCache();
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

    private function run(ExecutionRoute $route): mixed
    {
        $endpoint = $route->endpoint;
        if ($endpoint->isCallable()) {
            $callable = $this->diResolver->resolveCallable($endpoint->getCallable());
            return run_callable($callable);
        }
        if ($endpoint->isController()) {
            $controllerReflection = new ControllerReflection($this->request, $this->di, $this->diResolver, $endpoint->getControllerActionList());
            $controllerReflection->validate();
            return $controllerReflection->invoke();
        }

        return null;
    }
}