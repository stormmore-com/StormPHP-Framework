<?php

namespace Stormmore\Framework\Mvc\Route;

use closure;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\SourceCode\SourceCode;

class Router
{
    private array $routes = [];

    public function __construct(private readonly SourceCode $sourceCode)
    {
    }

    public function addRoute(string $key, callable|string $value): void
    {
        $this->routes[$key] = $value;
    }

    public function find(Request $request): ?ExecutionRoute
    {
        $requestUri = $request->path;

        [$target, $parameters] = $this->findTarget($request->path);

        if ($target) {
            if (is_array($target)) {
                $target = $this->filterTargets($request, $target);
                if ($target == null) {
                    return null;
                }
            }
            return new ExecutionRoute($requestUri, $this->createEndpoint($target), $parameters);
        }

        return null;
    }

    private function filterTargets(Request $request, array $targets): null|array
    {
        foreach($targets as $target) {
            $types = $target[2];
            if (count($types) == 0) {
                return $target;
            }
            if (in_array($request->method, $types)) {
                return $target;
            }
        }
        return null;
    }

    private function findTarget(string $requestUri): array {
        $routes = $this->getAllRoutes();

        if (array_key_exists($requestUri, $routes)) {
            return [$routes[$requestUri], []];
        }
        else {
            $requestSegments = none_empty_explode("/", $requestUri);
            foreach (array_keys($routes) as $route) {
                if (substr_count($route, "/") == substr_count($requestUri, "/")) {
                    $routeSegments = none_empty_explode("/", $route);
                    if (($parameters = $this->matchSegments($routeSegments, $requestSegments))){
                        return [$routes[$route], $parameters];
                    }
                }
            }
        }
        return [];
    }

    private function getAllRoutes(): array
    {
        return array_merge($this->routes, $this->sourceCode->getRoutes());
    }

    private function createEndpoint(closure|array|string $target): Endpoint
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