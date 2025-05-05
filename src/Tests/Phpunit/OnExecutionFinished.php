<?php

namespace Stormmore\Framework\Tests\Phpunit;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;

class OnExecutionFinished implements ExecutionFinishedSubscriber
{
    public function notify(ExecutionFinished $event): void
    {
    }
}