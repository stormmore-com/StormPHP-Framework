<?php

namespace Infrastructure\Configuration;

use Stormmore\Framework\Configuration\IConfiguration;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\Request\Request;

readonly class AppUserConfiguration implements IConfiguration
{
    public function __construct(private AppUser $appUser, private Request $request)
    {
    }

    public function configure(): void
    {
        if ($this->request->cookies->has('session')) {
            $session = json_decode($this->request->cookies->get('session'));
            $this->appUser->authenticate();
            $this->appUser->name = $session->username;
            $this->appUser->setPrivileges($session->privileges);
        };
    }
}