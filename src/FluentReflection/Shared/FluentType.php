<?php

namespace Stormmore\Framework\FluentReflection\Shared;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionProperty;
use ReflectionParameter;
use Exception;
use DateTime;

class FluentType
{
    private array $builtInTypes = ['bool', 'int', 'float', 'string', 'array', 'DateTime', 'object', 'resource'];
    private array $typeDictionary = [
        'boolean' => 'bool',
        'integer' => 'int',
        'double' => 'float',
        'string' => 'string',
        'array' => 'array',
        'object' => 'object',
        'resource' => 'resource',
        'resource (closed)' => 'resource',
        'NULL' => 'null',
        'unknown type' => 'unknown type',
    ];

    public array $names = [];

    public function __construct(private readonly ReflectionParameter|ReflectionProperty $reflection)
    {
        $type = $this->reflection->getType();
        $this->names = [];
        if ($type instanceof ReflectionUnionType){
            foreach($type->getTypes() as $t) {
                $this->names[] = $t->getName();
            }
        }
        if ($type instanceof ReflectionNamedType){
            $this->names[] = $type->getName();
        }
        return $this->names;
    }

    public function isTyped(): bool
    {
        return count($this->names) > 0;
    }

    public function hasUserDefinedTypes(): bool
    {
        return $this->isTyped() and count(array_diff($this->names, $this->builtInTypes)) > 0;
    }

    public function getUserDefinedTypes(): array
    {
       return array_diff($this->names, $this->builtInTypes);
    }

    public function cast(mixed $value): SafeValue
    {
        if (!$this->isTyped()) {
            return new SafeValue(true, $value);
        }

        $valueType = $this->typeDictionary[gettype($value)];
        if ($valueType == 'object') {
            $valueType = get_class($value);
        }
        if (in_array($valueType, $this->names, true)) {
            return new SafeValue(true, $value);
        }

        foreach($this->names as $type) {
            if (($type == 'string' and is_string($value)) or
                ($type == 'bool' and is_bool($value)) or
                ($type == 'int' and is_numeric($value)) or
                ($type == 'float' and is_numeric($value)) or
                ($type == 'array' and is_array($value)))
            {
                return new SafeValue(true, $value);
            }
            if ($type == 'bool' and in_array($value, ['1', '0'])) {
                return new SafeValue(true, $value === "1");
            }
            if ($type == 'DateTime' and is_string($value)) {
                try {
                    return new SafeValue(true, new DateTime($value));
                }
                catch(Exception) { }
            }
        }

        if ($this->hasDefaultValue()) {
            return new SafeValue(true, $this->reflection->getDefaultValue());
        }
        else if ($this->reflection->getType()->allowsNull()) {
            return new SafeValue(true, null);
        }

        return new SafeValue(false, null);
    }

    public function getDefaultValue(): mixed
    {
        return $this->reflection->getDefaultValue();
    }

    public function hasDefaultValue(): bool
    {
        if ($this->reflection instanceof ReflectionProperty) {
            return $this->reflection->hasDefaultValue();
        }
        if ($this->reflection instanceof ReflectionParameter) {
            return $this->reflection->isDefaultValueAvailable();
        }
        return false;
    }
}