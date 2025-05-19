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

    public function run(closure $next, array $options = []): void
    {
        $this->appConfiguration->addErrors([
            404 => '@templates/errors/404.php',
            'unauthenticated' => redirect('/signin'),
            'unauthorized' => redirect('/signin'),
            500 => '@templates/errors/500.php'
        ]);
        $next();
    }
}