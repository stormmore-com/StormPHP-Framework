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

    public static function update($filename, $changes): void
    {
        $filename = Path::resolve_alias($filename);
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $output = [];
        $currentSection = null;

        foreach ($lines as $line) {
            $trim = trim($line);

            if (preg_match('/^\[(.+)\]$/', $trim, $m)) {
                $currentSection = $m[1];
                $output[] = $line;
                continue;
            }

            if ($trim === '' || $trim[0] === ';' || $trim[0] === '#') {
                $output[] = $line;
                continue;
            }

            if (preg_match('/^([^=]+)=(.*)$/', $line, $m)) {
                $key = trim($m[1]);
                $val = trim($m[2]);

                if ($currentSection !== null &&
                    isset($changes[$currentSection][$key])) {
                    $newVal = $changes[$currentSection][$key];
                    $line = "$key = \"$newVal\"";
                    unset($changes[$currentSection][$key]); // zmiana zużyta
                } elseif ($currentSection === null &&
                    isset($changes[$key])) {
                    $newVal = $changes[$key];
                    $line = "$key = \"$newVal\"";
                    unset($changes[$key]);
                }
            }

            $output[] = $line;
        }

        foreach ($changes as $section => $values) {
            if (is_array($values)) {
                $output[] = "[$section]";
                foreach ($values as $key => $val) {
                    $output[] = "$key = \"$val\"";
                }
            } else {
                $output[] = "$section = \"$values\"";
            }
        }

        file_put_contents($filename, implode(PHP_EOL, $output) . PHP_EOL);
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