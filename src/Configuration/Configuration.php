<?php

namespace Stormmore\Framework\Configuration;

use Stormmore\Framework\Std\Path;

class Configuration
{
    protected array $configuration = [];

    public static function createFromFile(string $filename): Configuration
    {
        $configuration = new Configuration();
        $configuration->loadFile($filename);
        return $configuration;
    }

    public function set(string $name, string $value)
    {
        $this->configuration[$name] = $value;
    }

    public function loadFile(string $file): void
    {
        $file = Path::resolve_alias($file);
        $this->configuration = array_merge($this->configuration, parse_ini_file($file));
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

    public function getBool(string $name): bool
    {
        if (array_key_exists($name, $this->configuration)) {
            $value =  strtolower($this->configuration[$name]);
            return in_array($value, ["1", "true", "yes"]);
        }
        return false;
    }

    public function getArray(string $name, string $separator = ","): array
    {
        if (array_key_exists($name, $this->configuration)) {
            $value = $this->configuration[$name];
            return array_map(fn($item) => trim($item), explode($separator, $value));
        }

        return [];
    }
}