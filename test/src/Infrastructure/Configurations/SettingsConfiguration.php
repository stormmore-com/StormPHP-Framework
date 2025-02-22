<?php

namespace Infrastructure\Configurations;

use Infrastructure\Settings\Settings;
use Stormmore\Framework\App\IConfiguration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Configuration\JsonConfigurationLoader;

class SettingsConfiguration implements IConfiguration
{
    public function __construct(private Container $container, private JsonConfigurationLoader $settingsLoader)
    {
    }

    public function configure(): void
    {
        $settings = new Settings();
        $this->settingsLoader->load($settings, '@/settings.json');
        $this->container->register($settings);
    }
}