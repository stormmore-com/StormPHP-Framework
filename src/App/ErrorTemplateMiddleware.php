<?php

namespace Stormmore\Framework\App;

use closure;
use Stormmore\Framework\AppConfiguration;

class ErrorTemplateMiddleware implements IMiddleware
{
    public function __construct(private AppConfiguration $appConfiguration)
    {
    }

    public function run(closure $next, array $options = []): void
    {
        $this->appConfiguration->addErrors($options);

        $next();
    }
}