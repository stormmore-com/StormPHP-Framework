<?php

namespace Stormmore\Framework\FluentReflection;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class ReflectionTypeGetter
{
    /**
     * @return string[]
     */
    public static function getTypes(ReflectionProperty $property): array
    {
        $type = $property->getType();
        if ($type instanceof ReflectionUnionType){
            $types = [];
            foreach($type->getTypes() as $type){
                $types[] = $type->getName();
            }
            return $types;
        }
        if ($type instanceof ReflectionNamedType){
            return [$type->getName()];
        }
    }
}