<?php

namespace Stormmore\Framework;

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
        $this->environment = getenv("STORM_ENV");
    }

    public function isDevelopment(): bool
    {
        return str_starts_with($this->environment, 'development');
    }

    public function getCacheDirectory(): string
    {
        if ($this->cacheDirectory) {
            return $this->cacheDirectory;
        }
        return concatenate_paths($this->sourceDirectory, "/storm-cache/");
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