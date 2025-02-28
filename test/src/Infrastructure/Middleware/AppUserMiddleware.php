<?php

namespace Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Request\Request;

readonly class AppUserMiddleware implements IMiddleware
{
    public function __construct(private AppUser $appUser, private Request $request)
    {
    }

    public function run(closure $next): void
    {
        if ($this->request->cookies->has('session')) {
            $session = json_decode($this->request->cookies->get('session'));
            $this->appUser->authenticate();
            $this->appUser->name = $session->username;
            $this->appUser->setPrivileges($session->privileges);
        }

        $next();
    }
}