<?php

namespace Stormmore\Framework\Configuration;

class Configuration
{
    protected array $configuration = [];

    public static function createFromFile(string $filename): Configuration
    {
        $configuration = new Configuration();
        $configuration->loadFile($filename);
        return $configuration;
    }

    public function add(string $name, string $value)
    {
        $this->configuration[$name] = $value;
    }

    public function loadFile(string $file): void
    {
        $confFileLoader = new ConfFileLoader($file);
        $this->configuration = array_merge($this->configuration, $confFileLoader->parse());
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->configuration);
    }

    public function get(string $name, mixed $defaultValue = null): mixed
    {
        if (!array_key_exists($name, $this->configuration)) {
            return $defaultValue;
        }
        return $this->configuration[$name];
    }
}