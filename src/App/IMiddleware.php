<?php

namespace Stormmore\Framework\App;

use closure;

interface IMiddleware
{
    public function run(closure $next): void;
}