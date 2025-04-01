<?php

namespace Stormmore\Framework;

use Error;
use Stormmore\Framework\Configuration\Configuration;

class AppConfiguration
{
    private Configuration $configuration;

    public string $projectDirectory;
    public string $sourceDirectory;
    public string $cacheDirectory;
    public array $aliases = array();
    public array $errors = array();

    function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->configuration->set('environment', 'production');
        $this->configuration->set('logger.enabled', 'true');
        $this->configuration->set('logger.level', 'debug');
        $this->configuration->set('logger.directory', '@/.logs/');
    }

    public function isLoggerEnabled(): bool
    {
        return $this->configuration->getBool('logger.enabled');
    }

    public function getLogLevel(): string
    {
        return $this->configuration->get('logger.level');
    }

    public function setLogLevel(string $level): void
    {
        $this->configuration->set('logger.level', $level);
    }

    public function isDevelopment(): bool
    {
        return str_starts_with($this->configuration->get('environment'), 'development');
    }

    public function getEnvironment(): string
    {
        return $this->configuration->get('environment');
    }

    public function setProjectDirectory(string $projectDirectory): void
    {
        if (empty($projectDirectory)) {
            $projectDirectory = getcwd();
        }
        if (!file_exists($projectDirectory)) {
            throw new Error("Project directory '$projectDirectory' does not exist: ");
        }
        $this->projectDirectory = realpath($projectDirectory);
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
        if (empty($sourceDirectory)) {
            $sourceDirectory = $this->projectDirectory;
        }
        if (!file_exists($sourceDirectory)) {
            throw new Error("Source directory '$sourceDirectory' does not exist: ");
        }
        $this->sourceDirectory = realpath($sourceDirectory);
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        if (empty($cacheDirectory)) {
            $cacheDirectory = $this->projectDirectory . "/.cache";
        }

        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }
        $this->cacheDirectory = realpath($cacheDirectory);
    }

    public function getLoggerDirectory(): string
    {
        return $this->configuration->get('logger.directory');
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function addAliases(array $aliases): void
    {
        $this->aliases = array_merge($this->aliases, $aliases);
    }

    public function addErrors(array $errors): void
    {
        foreach($errors as $code => $error) {
            $this->errors[$code] = $error;
        }
    }
}