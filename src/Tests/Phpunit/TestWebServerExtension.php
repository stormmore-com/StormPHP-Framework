<?php

namespace Stormmore\Framework\Tests\Phpunit;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Stormmore\Framework\Tests\TestWebServer;

class TestWebServerExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $directory = $parameters->get('directory');
        $port = $parameters->get('port');
        $testWebServer = new TestWebServer($directory, $port);

        $facade->registerSubscriber(new RunTestWebServer($testWebServer));
        $facade->registerSubscriber(new ShutdownTestWebServer($testWebServer));
    }
}