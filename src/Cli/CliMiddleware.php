<?php

namespace Stormmore\Framework\Cli;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\App\RequestContext;

class CliMiddleware implements IMiddleware
{
    public function __construct(private RequestContext $requestContext,
                                private CliCommandRunner $commandRunner)
    {
    }

    public function run(closure $next): void
    {
        if ($this->requestContext->isCliCommand()) {
            $this->commandRunner->run();
        }
        else {
            $next();
        }
    }
}