<?php

namespace Stormmore\Framework\Tests\Phpunit;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use Stormmore\Framework\Tests\TestWebServer;

readonly class ShutdownTestWebServer implements ExecutionFinishedSubscriber
{
    public function __construct(private TestWebServer $testWebServer)
    {
    }

    public function notify(ExecutionFinished $event): void
    {
        $this->testWebServer->shutdown();
    }
}