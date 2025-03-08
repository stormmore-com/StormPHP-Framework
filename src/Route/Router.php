<?php

namespace Stormmore\Framework\Route;

use closure;
use Stormmore\Framework\SourceCode\SourceCode;
use Stormmore\Framework\Request\Request;

class Router
{
    private array $routes = [];

    public function __construct(private SourceCode $sourceCode)
    {
    }

    public function addRoute($key, $value): void
    {
        $this->routes[$key] = $value;
    }

    public function addRoutes(array $routes): void
    {
        $this->routes = array_merge($this->routes, $routes);
    }

    public function find(Request $request): ?ExecutionRoute
    {
        $requestUri = $request->requestUri;
        foreach ($this->getAllRoutes() as $pattern => $target) {
            if ($pattern == $requestUri) {
                return new ExecutionRoute($pattern, $this->createEndpoint($target));
            }
        }

        $requestSegments = none_empty_explode("/", $requestUri);
        foreach ($this->getAllRoutes() as $route => $target) {
            if (substr_count($route, "/") == substr_count($requestUri, "/")) {
                $routeSegments = none_empty_explode("/", $route);
                $parameters = $this->matchSegments($routeSegments, $requestSegments);
                if ($parameters) {
                    return new ExecutionRoute($route, $this->createEndpoint($target), $parameters);
                }
            }
        }
        return null;
    }

    private function getAllRoutes(): array
    {
        return [$this->routes, ...$this->sourceCode->getRoutes()];
    }

    private function createEndpoint(closure|array $target): Endpoint
    {
        return new Endpoint($target);
    }

    private function matchSegments(array $routeSegments, array $requestSegments): ?array
    {
        $parameters = [];
        foreach ($routeSegments as $i => $routeSegment) {
            if (str_starts_with($routeSegment, ":")) {
                $name = str_replace(":", "", $routeSegment);
                $parameters[$name] = $requestSegments[$i];
            } else if ($routeSegment != $requestSegments[$i]) {
                return null;
            }
        }

        return $parameters;
    }
}