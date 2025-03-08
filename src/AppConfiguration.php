<?php

namespace Stormmore\Framework;

use Error;
use Stormmore\Framework\Authentication\AppUser;

class AppConfiguration
{
    public string $projectDirectory;
    public string $sourceDirectory;
    public ?string $cacheDirectory = null;
    public ?string $baseUrl = null;
    public ?string $environment = null;
    public array $aliases = array();
    public array $errors = array();

    function __construct()
    {
        $env = getenv('STORM_ENV');
        if (!$env) {
            $env = 'development';
        }
        $this->environment = $env;
    }

    public function isDevelopment(): bool
    {
        return str_starts_with($this->environment, 'development');
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
            $sourceDirectory = getcwd();
        }
        if (!file_exists($sourceDirectory)) {
            throw new Error("Source directory '$sourceDirectory' does not exist: ");
        }
        $this->sourceDirectory = realpath($sourceDirectory);
    }

    public function setCacheDirectory(string $cacheDirectory): void
    {
        if (empty($cacheDirectory)) {
            $cacheDirectory = $this->projectDirectory;
        }

        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }
        $this->cacheDirectory = realpath($cacheDirectory);
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