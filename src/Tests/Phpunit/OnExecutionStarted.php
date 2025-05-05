<?php

namespace Stormmore\Framework\Tests\Phpunit;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;

class OnExecutionStarted implements ExecutionStartedSubscriber
{
    public function notify(ExecutionStarted $event): void
    {
    }
}