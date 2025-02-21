<?php

namespace Infrastructure\Configurations;

use Infrastructure\Settings;
use Stormmore\Framework\App\IConfiguration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Settings\SettingsLoader;

class SettingsConfiguration implements IConfiguration
{
    public function __construct(private Container $container, private SettingsLoader $settingsLoader)
    {
    }

    public function configure(): void
    {
        $settings = new Settings();
        $this->settingsLoader->load($settings, '@/settings.json');
        $this->container->register($settings);
    }
}