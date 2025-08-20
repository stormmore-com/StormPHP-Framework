<?php

namespace Stormmore\Framework\App;

use closure;
use Exception;
use Stormmore\Framework\DependencyInjection\Resolver;

class MiddlewareChain
{
    private array $middlewares;
    private array $options = [];

    public function __construct(private readonly Resolver $resolver)
    {
    }

    public function add(string $middleware, closure|string|array $options = []): MiddlewareChain
    {
        $this->middlewares[] = $middleware;
        $this->options[count($this->middlewares) - 1] = $options;

        return $this;
    }

    public function run(): void
    {
        $first = $this->getMiddlewareAsCallable(0);
        $first();
    }

    private function getMiddlewareAsCallable(int $i): closure
    {
        if ($i >= count($this->middlewares)) {
            return function() { };
        }
        return function() use ($i) {
            $options = array_key_value($this->options, $i, []);
            $className = $this->middlewares[$i];
            $middleware = $this->resolver->resolve($className);
            $middleware instanceof IMiddleware or throw new Exception("Class `$className` does not implement IMiddleware interface");
            $middleware->run($this->getMiddlewareAsCallable($i + 1), $options);
        };
    }
}