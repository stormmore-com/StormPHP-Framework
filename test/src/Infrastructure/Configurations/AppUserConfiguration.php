<?php

namespace Infrastructure\Configurations;

use Infrastructure\Settings\Settings;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Authentication\IdentityUser;
use Stormmore\Framework\Configuration\IConfiguration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Request\Request;

readonly class AppUserConfiguration implements IConfiguration
{
    public function __construct(private Container $container, private Request $request)
    {
    }

    public function configure(): void
    {
        if ($this->request->hasCookie('session')) {
            $session = json_decode($this->request->getCookie('session'));
            $appUser = new AppUser();
            $appUser->authenticate();
            $appUser->name = $session->username;
            $this->container->registerAs($appUser, AppUser::class);
            $this->container->register($appUser);
        }
    }
}