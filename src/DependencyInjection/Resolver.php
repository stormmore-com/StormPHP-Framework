<?php

namespace Stormmore\Framework\DependencyInjection;

use Exception;
use Stormmore\Framework\FluentReflection\Class\FluentClass;
use Stormmore\Framework\FluentReflection\Class\FluentClassParameter;
use Stormmore\Framework\FluentReflection\Shared\FluentFunction;
use Stormmore\Framework\FluentReflection\Shared\IFluentParameterized;

readonly class Resolver
{
    public function __construct(private Container $di)
    {
    }

    public function resolve(string|callable|FluentClass $toResolve): object|callable
    {
        if (is_callable($toResolve)) {
            return $this->resolveCallable($toResolve);
        }
        if (is_string($toResolve)) {
            $toResolve = FluentClass::create($toResolve);
        }
        $args = [];
        if ($toResolve->hasConstructor()) {
            $args = $this->resolveParameters($toResolve->getConstructor());
        }
        return $toResolve->createInstance($args);
    }

    private function resolveCallable(callable $callable): callable|null
    {
        return function () use ($callable) {
            $args = [];
            $function = new FluentFunction($callable);
            $args[] = $this->resolveParameters($function);
            return $function->invoke($args);
        };
    }

    private function resolveParameters(IFluentParameterized $callable): array
    {
        $args = [];
        foreach ($callable->getParameters() as $parameter) {
            $arg = $this->resolveFluentParameter($parameter);
            $args[] = $arg;
        }
        return $args;
    }

    /**
     * @param FluentClassParameter $parameter
     * @return object
     * @throws Exception
     */
    private function resolveFluentParameter(FluentClassParameter $parameter): object
    {
        $names = [];
        if ($parameter->type->isTyped()) {
            foreach($parameter->type->names as $typeName) {
                if ($typeName == Container::class) {
                    return $this->di;
                }
                if (!$this->di->isRegistered($typeName)) {
                    $args = [];
                    $class = FluentClass::create($typeName);
                    if ($class->hasConstructor()) {
                        $args = $this->resolveParameters($class->getConstructor());
                    }
                    $this->di->register($class->createInstance($args));
                }
                return $this->di->resolve($typeName);
            }
        }

        $names[] = $parameter->getName();
        $names[] = ucfirst($parameter->getName());
        foreach ($names as $name) {
            if ($this->di->isRegistered($name)) {
                return $this->$name;
            }
        }

        throw new Exception();
    }
}