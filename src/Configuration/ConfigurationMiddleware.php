<?php

namespace Stormmore\Framework\Configuration;

use closure;
use Stormmore\Framework\App\IMiddleware;

readonly class ConfigurationMiddleware implements IMiddleware
{
    public function __construct(private Configuration $configuration)
    {
    }

    public function run(closure $next, mixed $options = []): void
    {
        foreach($options as $file) {
            $this->configuration->loadFile($file);
        }
        $next();
    }
}