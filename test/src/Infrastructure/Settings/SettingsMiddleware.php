<?php

namespace Infrastructure\Settings;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\JsonConfigurationLoader;
use Stormmore\Framework\DependencyInjection\Container;

readonly class SettingsMiddleware implements IMiddleware
{
    public function __construct(private Container $container, private JsonConfigurationLoader $jsonConfigurationLoader)
    {
    }

    public function run(closure $next): void
    {
        $settings = new Settings();
        $this->jsonConfigurationLoader->load($settings, '@/settings.json');
        $this->container->register($settings);
        $next();
    }
}



