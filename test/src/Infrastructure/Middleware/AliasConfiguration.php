<?php

namespace Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\AppConfiguration;

readonly class AliasConfiguration implements IMiddleware
{
    public function __construct(private AppConfiguration $appConfiguration)
    {
    }

    public function run(closure $next): void
    {
        $this->appConfiguration->addAliases([
            '@templates' => "@/src/templates"
        ]);
        $next();
    }
}