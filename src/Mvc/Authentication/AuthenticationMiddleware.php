<?php

namespace Stormmore\Framework\Mvc\Authentication;

use closure;
use Exception;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\DependencyInjection\Resolver;

readonly class AuthenticationMiddleware implements IMiddleware
{
    public function __construct(private AppUser $appUser, private Resolver $resolver)
    {
    }

    public function run(closure $next, mixed $options = null): void
    {
        if (is_callable($options)) {
            $options($this->appUser);
        }
        else if (is_string($options)) {
            $object = $this->resolver->resolve($options);
            if ($object instanceof IAuthenticatorConf) {
                $object->authenticate($this->appUser);
            }
            else {
                throw new Exception(
                    "Resolved object for option '{$options}' is not a valid IAuthenticatorConf. Got "
                    . (is_object($object) ? get_class($object) : gettype($object)) . " instead."
                );
            }

        }
        else {
            throw new Exception("AuthenticationMiddleware option method must be a class name or a callable");
        }

        $next();
    }
}