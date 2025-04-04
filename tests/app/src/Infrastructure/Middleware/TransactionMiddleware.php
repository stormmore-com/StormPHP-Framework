<?php

namespace src\Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Throwable;

class TransactionMiddleware implements IMiddleware
{
    public function run(closure $next): void
    {
        try {
            //open transaction
            $next();
            //close transaction
        }
        catch(Throwable $throwable) {
            //rollback transaction
            throw $throwable;
        }
    }
}