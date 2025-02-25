<?php

namespace Stormmore\Framework\Mvc;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Stormmore\Framework\Authentication\AjaxAuthenticate;
use Stormmore\Framework\Authentication\AjaxAuthenticationException;
use Stormmore\Framework\Authentication\Authenticate;
use Stormmore\Framework\Authentication\AuthenticationException;
use Stormmore\Framework\Authentication\Authorize;
use Stormmore\Framework\Authentication\AuthorizedException;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Request\Request;

readonly class ControllerReflection
{
    private ReflectionClass $class;
    private ReflectionMethod $method;

    public function __construct(private Request   $request,
                                private Container $di,
                                private Resolver  $diResolver,
                                private array     $endpoint)
    {
        $this->class = new ReflectionClass($this->endpoint[0]);
        $this->method = $this->class->getMethod($this->endpoint[1]);
    }

    public function validate(): void
    {
        $this->validateAjaxAuthentication($this->class, $this->method);
        $this->validateRequestType($this->class, $this->method);
        $this->validateAuthentication($this->class, $this->method);
        $this->validateClaims($this->class, $this->method);
    }

    public function invoke(): mixed
    {
        $obj = $this->diResolver->resolveObject($this->endpoint[0]);
        $args = $this->diResolver->resolveReflectionMethod($this->method);
        return $this->method->invokeArgs($obj, $args);
    }

    /**
     * @throws AjaxAuthenticationException with code 401 if request is not authenticated
     */
    private function validateAjaxAuthentication(ReflectionClass $class, ReflectionMethod $method): void
    {
        if (count($class->getAttributes(AjaxAuthenticate::class)) or
            count($method->getAttributes(AjaxAuthenticate::class))) {
            $user = $this->di->resolve(AppUser::class);
            if (!$user->isAuthenticated()) {
                throw new AjaxAuthenticationException("APP: authentication required", 401);
            }
        }
    }

    /**
     * @throws Exception with code 404 if request method is different then required.
     */
    private function validateRequestType(ReflectionClass $class, ReflectionMethod $method): void
    {
        if (count($class->getAttributes(Post::class)) or
            count($method->getAttributes(Post::class))) {
            if (!$this->request->isPost()) {
                throw new Exception("POST required", 404);
            }
        }

        if (count($class->getAttributes(Get::class)) or
            count($method->getAttributes(Get::class))) {
            if (!$this->request->isGet()) {
                throw new Exception("GET required", 404);
            }
        }
    }

    private function validateAuthentication(ReflectionClass $class, ReflectionMethod $method): void
    {
        if (count($class->getAttributes(Authenticate::class)) or
            count($method->getAttributes(Authenticate::class))) {
            $user = $this->di->resolve(AppUser::class);
            if (!$user->isAuthenticated()) {
                throw new AuthenticationException("APP: authentication required", 401);
            }
        }
    }

    private function validateClaims(ReflectionClass $class, ReflectionMethod $method): void
    {
        $classAttributes = $class->getAttributes(Authorize::class);
        $methodAttributes = $method->getAttributes(Authorize::class);
        $classClaims = $this->getClaimsFromAttribute($classAttributes);
        $methodClaims = $this->getClaimsFromAttribute($methodAttributes);

        $requiredClaims = array_merge($classClaims, $methodClaims);

        if ($classAttributes or $methodAttributes) {
            $user = $this->di->resolve(AppUser::class);
            if (!$user->hasPrivileges($requiredClaims)) {
                throw new AuthorizedException("APP: Privilege required", 403);
            }
        }
    }

    private function getClaimsFromAttribute(array $attributes): array
    {
        if (count($attributes)) {
            return $attributes[0]->newInstance()->claims;
        }

        return [];
    }
}