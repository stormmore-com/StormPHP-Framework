<?php

namespace Stormmore\Framework\Configuration;

use Exception;
use ReflectionClass;
use Stormmore\Framework\UnknownPathAliasException;

class JsonConfigurationLoader
{
    /**
     * @throws UnknownPathAliasException
     */
    public function LoadIfExist(string|object $object, $filePath): ?object
    {
        if ($this->exist($filePath)) {
            return self::load($object, $filePath);
        }

        return null;
    }

    public function exist(string $filepath): bool
    {
        return file_exists(resolve_path_alias($filepath));
    }

    /**
     * @throws UnknownPathAliasException
     */
    public function load(string|object $object, $filePath): object
    {
        $filePath = resolve_path_alias($filePath);
        is_file($filePath) or throw new Exception("SettingsLoader: File $filePath doesn't exist");
        $json = json_decode(file_get_contents($filePath));

        if (is_string($object)) {
            class_exists($object) or throw new Exception("SettingsLoader: Class $object doesn't exist");
            $object = new $object;
        }

        self::map($json, $object);

        return $object;
    }

    private function map($source, $destination): void
    {
        if ($source == null) return;

        $reflection = new ReflectionClass($destination);
        foreach (get_object_vars($source) as $name => $value) {
            $setMethodName = "set" . ucfirst($name);
            if ($reflection->hasMethod($setMethodName)) {
                $reflection->getMethod($setMethodName)->invoke($destination, $value);
                continue;
            }
            $reflection->hasProperty($name) or
            throw new Exception("SettingsLoader: settings doesn't have property [$name]");

            $property = $reflection->getProperty($name);
            $type = $property->getType();
            if (is_object($value) and $type == 'array') {
                $reflection->getProperty($name)?->setValue($destination, (array)$value);
            } else if (is_object($value) && $property->hasType()) {
                $propertyValueObject = $property->getValue($destination);
                self::map($value, $propertyValueObject);
            } else {
                $reflection->getProperty($name)?->setValue($destination, $value);
            }
        }
    }
}