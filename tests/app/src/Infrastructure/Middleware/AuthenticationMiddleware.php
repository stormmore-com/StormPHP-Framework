<?php

namespace src\Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\IO\Request\Request;

readonly class AuthenticationMiddleware implements IMiddleware
{
    public function __construct(private AppUser $appUser, private Request $request)
    {
    }

    public function run(closure $next, array $options = []): void
    {
        if ($this->request->hasCookie('session')) {
            $session = json_decode($this->request->getCookie('session')->getValue());
            $this->appUser->authenticate();
            $this->appUser->name = $session->username;
            $this->appUser->setPrivileges($session->privileges);
        }

        $next();
    }
}