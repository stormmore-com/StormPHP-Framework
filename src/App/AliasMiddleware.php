<?php

namespace Stormmore\Framework\App;

use closure;
use Stormmore\Framework\AppConfiguration;

readonly class AliasMiddleware implements IMiddleware
{
    public function __construct(private AppConfiguration $appConfiguration)
    {
    }

    public function run(closure $next, mixed $options = []): void
    {
        $this->appConfiguration->addAliases($options);

        $next();
    }
}