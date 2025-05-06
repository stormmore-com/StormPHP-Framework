<?php

namespace Stormmore\Framework\Mvc;

use Exception;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\FluentReflection\Class\FluentClass;
use Stormmore\Framework\FluentReflection\Class\FluentClassMethod;
use Stormmore\Framework\Mvc\Attributes\Delete;
use Stormmore\Framework\Mvc\Attributes\Get;
use Stormmore\Framework\Mvc\Attributes\Patch;
use Stormmore\Framework\Mvc\Attributes\Post;
use Stormmore\Framework\Mvc\Attributes\Put;
use Stormmore\Framework\Mvc\Authentication\Ajax;
use Stormmore\Framework\Mvc\Authentication\AjaxAuthenticationException;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\Authentication\Authenticate;
use Stormmore\Framework\Mvc\Authentication\AuthenticationException;
use Stormmore\Framework\Mvc\Authentication\Authorize;
use Stormmore\Framework\Mvc\Authentication\AuthorizedException;
use Stormmore\Framework\Mvc\IO\Request\Request;

readonly class ControllerReflection
{
    private FluentClass $fluentClass;
    private FluentClassMethod $fluentMethod;
    private ControllerActionArguments $actionArguments;

    public function __construct(private Request   $request,
                                private Container $di,
                                private Resolver  $resolver,
                                private array     $endpoint)
    {
        $this->fluentClass = FluentClass::create($this->endpoint[0]);
        $this->fluentMethod = $this->fluentClass->methods->getMethod($this->endpoint[1]);
        $this->actionArguments = new ControllerActionArguments($this->fluentMethod->getParameters(), $this->request, $this->resolver);
    }

    public function validate(): void
    {
        $user = $this->di->resolve(AppUser::class);
        if ($this->endpointHasAttribute(Ajax::class)) {
            $user->isAuthenticated() or throw new AjaxAuthenticationException("APP: authentication required", 401);
        }

        $methodAttributes = [
            'GET' => Get::class,
            'POST' => Post::class,
            'PUT' => Put::class,
            'PATCH' => Patch::class,
            'DELETE' => Delete::class];
        foreach($methodAttributes as $name => $class) {
            if ($this->endpointHasAttribute($class)) {
                $this->request->is($name) or throw new Exception("$name required", 404);
            }
        }

        if ($this->endpointHasAttribute(Authenticate::class)) {
            $user->isAuthenticated() or throw new AuthenticationException("APP: authentication required", 401);
        }

        $classClaims = $this->fluentClass->getAttributes(Authorize::class)->select(function($x) {return $x->getInstance()->claims;});
        $methodClaims = $this->fluentMethod->getAttributes(Authorize::class)->select(function($x) {return $x->getInstance()->claims;});
        $requiredClaims = array_merge(...$classClaims, ...$methodClaims);
        if (count($requiredClaims)) {
            $user->hasPrivileges($requiredClaims) or throw new AuthorizedException("APP: Privilege required", 403);
        }
    }

    private function endpointHasAttribute(string $name): bool
    {
        return $this->fluentClass->hasAttribute($name) or $this->fluentMethod->hasAttribute($name);
    }

    public function invoke(): mixed
    {
        $this->actionArguments->areValid() or throw new Exception("Invalid parameters `{$this->request->path}`", 400);
        $arguments = $this->actionArguments->getArguments();
        $obj = $this->resolver->resolve($this->fluentClass);
        return $this->fluentMethod->invoke($obj, $arguments);
    }
}