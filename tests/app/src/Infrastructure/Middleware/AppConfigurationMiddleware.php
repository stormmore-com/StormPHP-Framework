<?php

namespace src\Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\AppConfiguration;

readonly class AppConfigurationMiddleware implements IMiddleware
{
    public function __construct(private AppConfiguration $appConfiguration)
    {
    }

    public function run(closure $next): void
    {
        $this->appConfiguration->addAliases([
            '@templates' => "@/templates"
        ]);
        $this->appConfiguration->addErrors([
            404 => '@templates/errors/404.php',
            'unauthenticated' => redirect('/signin'),
            'unauthorized' => redirect('/signin'),
            'default' => '@templates/errors/500.php'
        ]);
        if ($this->appConfiguration->isDevelopment()) {
            $this->appConfiguration->addErrors([
                'default' => '@templates/errors/500_dev.php'
            ]);
        }
        $next();
    }
}