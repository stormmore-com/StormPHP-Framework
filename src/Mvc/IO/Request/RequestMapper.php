<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use Stormmore\Framework\FluentReflection\Object\FluentObject;
use AllowDynamicProperties;

class RequestMapper
{
    public static function map(Request $request, object $to, null|array $array = []): object
    {
        $reflection = new FluentObject($to);
        foreach($request->getAll() as $key => $value) {
            if ($reflection->properties->exist($key)) {
                $property = $reflection->properties->get($key);
                $castedValue = $property->type->cast($value);
                if ($castedValue->exist) {
                    $property->setValue($castedValue->value);
                }
            }
            else if (self::canDynamicallyCreateProp($reflection)) {
                $to->{$key} = $value;
            }
        }
        return $to;
    }

    private static function canDynamicallyCreateProp(FluentObject $reflection): bool
    {
        if (!class_exists("AllowDynamicProperties")) {
            return true;
        }
        return $reflection->hasAttribute(AllowDynamicProperties::class);
    }
}