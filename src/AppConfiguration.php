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
    public null|string $templatesDirectory = null;
    public array $aliases = array();
    public array $errors = array();

    function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->configuration->set('environment', 'production');
        $this->configuration->set('logger.enabled', 'true');
        $this->configuration->set('logger.level', 'debug');
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

    public function isProduction(): bool
    {
        return str_starts_with($this->configuration->get('environment'), 'production');
    }

    public function getEnvironment(): string
    {
        if ($this->isDevelopment())
        {
            return 'development';
        }
        return 'production';
    }

    public function setDirectories(array $directories): void
    {
        $project = getcwd();
        if (array_key_exists('project', $directories)) {
            $project = $directories['project'];
        }
        is_dir($project) or throw new Error("Project directory '$project' does not exist.");
        $this->projectDirectory = realpath($project);

        $source = getcwd();
        if (array_key_exists('source', $directories)) {
            $source = $directories['source'];
        }
        is_dir($source) or throw new Error("Source directory '$source' does not exist.");
        $this->sourceDirectory = realpath($source);

        $cache = $source . "/.cache";
        if (array_key_exists('cache', $directories)) {
            $cache = $directories['cache'];
        }
        if (!is_dir($cache)) {
            mkdir($cache, 0777, true);
        }
        $this->cacheDirectory = realpath($cache);

        if (array_key_exists('templates', $directories)) {
            $templatesDirectory = $directories['templates'];
            if (is_dir($templatesDirectory)) {
                $this->templatesDirectory = $templatesDirectory;
            }
        }

        $logs = $project . "/.logs";
        if (array_key_exists('logs', $directories)) {
            $logs = $directories['logs'];
        }
        if (!is_dir($logs)) {
            mkdir($logs, 0777, true);
        }
        $this->configuration->set('logger.directory', realpath($logs));
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