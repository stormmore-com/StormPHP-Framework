<?php

namespace Infrastructure\Configurations;

use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Configuration\IConfiguration;
use Stormmore\Framework\Request\Request;

readonly class AppUserConfiguration implements IConfiguration
{
    public function __construct(private AppUser $appUser, private Request $request)
    {
    }

    public function configure(): void
    {
        if ($this->request->cookies->has('session')) {
            $session = json_decode($this->request->cookies->get('session')->getValue());
            $this->appUser->authenticate();
            $this->appUser->name = $session->username;
            $this->appUser->setPrivileges($session->privileges);
        }
    }
}