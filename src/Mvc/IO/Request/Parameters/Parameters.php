<?php

namespace Stormmore\Framework\Mvc\IO\Request\Parameters;

use Stormmore\Framework\Std\Path;

class Parameters implements IParameters
{
    private array $parameters = [];

    public function __construct(array ...$parameters)
    {
        foreach ($parameters as $parameter) {
            $this->parameters = array_merge($parameter);
        }
    }

    public function has(array|string $name): bool
    {
        if (is_array($name))
        {
            $names = $name;
            foreach($names as $name) {
                if (!array_key_exists($name, $this->parameters)) {
                    return false;
                }
            }
            return true;
        }
        else {
            return array_key_exists($name, $this->parameters);
        }
    }

    public function get(array|string $name, $default = null): mixed
    {
        if (is_array($name)) {
            $names = $name;
            $parameters = [];
            foreach($names as $name) {
                $parameters[] = $this->parameters[$name] ?? $default;
            }
            return $parameters;
        }
        return $this->parameters[$name] ?? $default;
    }

    public function getBool(string $name, ?bool $default = null): ?bool
    {
        if ($this->has($name)) {
            $value = strtolower($this->get($name));
            if ($value == "true" or $value == "1") return true;
            if ($value == "false" or $value == "0") return false;
        }

        return $default;
    }

    public function getInt(string $name, ?int $defaultValue = null): ?int
    {
        $value = $this->get($name);
        if ($value and is_numeric($value)) {
            return intval($value);
        }
        return null;
    }

    public function getFloat(string $name, ?float $defaultValue = null): ?float
    {
        $value = $this->get($name);
        if ($value and is_numeric($value)) {
            return floatval($value);
        }
        return null;
    }

    public function getDateTime(string $name, ?DateTime $defaultValue = null): ?DateTime
    {
        $value = $this->get($name);
        if ($value) {
            try {
                return new DateTime($value);
            } catch (Exception) {
            }
        }
        return null;
    }

    public function toArray(): array
    {
        return $this->parameters;
    }
}