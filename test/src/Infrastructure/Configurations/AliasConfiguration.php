<?php

namespace Infrastructure\Configurations;

use Infrastructure\Settings\Settings;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Configuration\IConfiguration;

readonly class AliasConfiguration implements IConfiguration
{
    public function __construct(private AppConfiguration $appConfiguration, private Settings $settings)
    {
    }

    public function configure(): void
    {
        $this->appConfiguration->addAliases([
            '@templates' => "@/src/templates/"
        ]);
    }
}