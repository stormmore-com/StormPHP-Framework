<?php

namespace Infrastructure\Configuration;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Configuration\IConfiguration;

readonly class ErrorConfiguration implements IConfiguration
{
    public function __construct(private AppConfiguration $configuration)
    {
    }
    public function configure(): void
    {
        $this->configuration->addErrors([
            404 => '@templates/errors/404.php',
            'unauthenticated' => redirect('/signin'),
            'unauthorized' => redirect('/signin'),
            'default' => '@templates/errors/500.php'
        ]);
        if ($this->configuration->isDevelopment()) {
            $this->configuration->addErrors([
                'default' => '@templates/errors/500_dev.php'
            ]);
        }
    }
}