<?php

namespace Infrastructure\Configuration;

use Stormmore\Framework\Configuration\IConfiguration;
use Stormmore\Framework\Logger\Configuration;
use Stormmore\Framework\Logger\Logger;

class LoggerConfiguration implements IConfiguration
{
    public function __construct(private Configuration $configuration)
    {
    }

    public function configure(): void
    {
        $this->configuration->enabled = true;
        $this->configuration->level = Logger::INFO;
    }
}