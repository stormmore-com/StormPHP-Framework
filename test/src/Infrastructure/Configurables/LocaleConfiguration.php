<?php

namespace Configurables;

use Stormmore\Framework\App\IConfiguration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Request\Request;

readonly class LocaleConfiguration implements IConfiguration
{
    public function __construct(private Request $request, private Container $container)
    {
    }

    public function configure(): void
    {
        $this->container->register(new Locale('pl-PL'));
    }
}