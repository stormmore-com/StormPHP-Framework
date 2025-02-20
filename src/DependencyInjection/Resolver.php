<?php

namespace Stormmore\Framework\DependencyInjection;

use Exception;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionException;
use ReflectionParameter;
use ReflectionClass;

readonly class Resolver
{
    public function __construct(private Container $di)
    {
    }

    public function resolveCallable(callable|null $callable): callable|null
    {
        return function () use ($callable) {
            $reflection = new ReflectionFunction($callable);
            $args = $this->resolveReflectionFunction($reflection);
            return $reflection->invokeArgs($args);
        };
    }

    public function resolveObject(string $className): object
    {
        $reflectionClass = new ReflectionClass($className);
        $args = [];
        $constructor = $reflectionClass->getConstructor();
        if ($constructor) {
            $args = $this->resolveReflectionMethod($constructor);
        }
        return $reflectionClass->newInstanceArgs($args);
    }

    /**
     * @throws ResolverException
     */
    public function resolveReflectionMethod(ReflectionMethod $reflection): array
    {
        $args = [];
        try {
            $parameters = $reflection->getParameters();
            foreach ($parameters as $parameter) {
                $arg = $this->resolveParameter($parameter);
                $args[] = $arg;
            }
        } catch (Exception $e) {
            $class = $reflection->getDeclaringClass()->getName();
            $method = $reflection->getName();
            $prmName = $parameter->getName();
            $prmType = $parameter->getType();

            $parameter = $prmName;
            if ($prmType) {
                $parameter = $prmType . ' $' . $prmName;
            }
            $method == "__construct" ? $method = 'Constructur' : $method = "Method [$method]";
            $message = "Could not create [$class]. $method parameter [$parameter] can't be resolved.";
            throw new ResolverException($message, 0, $e);
        }

        return $args;
    }

    /**
     * @throws ReflectionException
     */
    public function resolveReflectionFunction(ReflectionFunction $reflection): array
    {
        $args = [];
        $parameters = $reflection->getParameters();
        foreach ($parameters as $parameter) {
            $arg = $this->resolveParameter($parameter);
            $args[] = $arg;
        }
        return $args;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    private function resolveParameter(ReflectionParameter $parameter): object
    {
        $names = [];
        if ($parameter->hasType()) {
            $typeName = $parameter->getType()->getName();
            if ($typeName == Container::class) {
                return $this->di;
            }

            if (!$this->di->isRegistered($typeName)) {
                $reflection = new ReflectionClass($typeName);
                $constructor = $reflection->getConstructor();
                if ($constructor == null) {
                    $this->di->register($reflection->newInstance());
                } else {
                    $args = $this->resolveReflectionMethod($constructor);
                    $instance = $reflection->newInstanceArgs($args);
                    $this->di->register($instance);
                }
            }
            return $this->di->resolve($typeName);
        }

        $names[] = $parameter->getName();
        $names[] = ucfirst($parameter->getName());
        foreach ($names as $name) {
            if ($this->di->isRegistered($name)) {
                return $this->$name;
            }
        }

        $parameterName = '$' . $parameter->getName();
        $functionName = $parameter->getDeclaringFunction()->getName();
        $className = $parameter->getDeclaringClass()?->getName();
        if ($className) {
            $functionName = $className . $functionName;
        }
        throw new Exception("DI: Function [$functionName()] parameter [$parameterName] not found");
    }
}