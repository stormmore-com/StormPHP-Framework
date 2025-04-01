<?php

namespace Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;

readonly class SettingsMiddleware implements IMiddleware
{
    public function __construct(private Configuration $configuration)
    {
    }

    public function run(closure $next): void
    {
        $this->configuration->loadFile('@/settings.conf');
        $next();
    }
}



