<?php

namespace src\Infrastructure;

use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\Authentication\IAuthenticatorConf;
use Stormmore\Framework\Mvc\IO\Request;

readonly class AuthenticationConf implements IAuthenticatorConf
{
    public function __construct(private Request $request)
    {
    }

    public function authenticate(AppUser $appUser): void
    {
        if ($this->request->hasCookie('session')) {
            $session = json_decode($this->request->getCookie('session')->getValue());
            $appUser->authenticate();
            $appUser->name = $session->username;
            $appUser->setPrivileges($session->privileges);
        }
    }
}