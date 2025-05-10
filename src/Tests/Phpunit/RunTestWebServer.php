<?php

namespace Stormmore\Framework\Tests\Phpunit;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use Stormmore\Framework\Tests\TestWebServer;

readonly class RunTestWebServer implements ExecutionStartedSubscriber
{
    public function __construct(private TestWebServer $testWebServer)
    {
    }

    public function notify(ExecutionStarted $event): void
    {
        $this->testWebServer->run();
    }
}