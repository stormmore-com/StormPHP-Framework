<?php

namespace Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\AppConfiguration;

readonly class ErrorConfiguration implements IMiddleware
{
    public function __construct(private AppConfiguration $configuration)
    {
    }
    public function run(closure $next): void
    {
        $this->configuration->addErrors([
            404 => '@templates/errors/404.php',
            'unauthenticated' => redirect('/signin'),
            'unauthorized' => redirect('/signin'),
            //'default' => '@templates/errors/500.php'
        ]);
        if ($this->configuration->isDevelopment()) {
            $this->configuration->addErrors([
            //    'default' => '@templates/errors/500_dev.php'
            ]);
        }

        $next();
    }
}