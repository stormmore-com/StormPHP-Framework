<?php

namespace Stormmore\Framework\Classes;

use PhpToken;
use Stormmore\Framework\Classes\Parser\PhpClassFileParser;
use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;

class RouteScanner
{
    public function scan(array $classes): array
    {
        $routes = [];
        foreach ($classes as $filePath) {
            $fileRoutes = $this->getClassFileRoutes($filePath);
            $routes = array_merge($routes, $fileRoutes);
        }

        uksort($routes, function ($key1, $key2) {
            $lengthMatch = substr_count($key2, "/") <=> substr_count($key1, "/");
            if ($lengthMatch) {
                return $lengthMatch;
            }
            return $key1 <=> $key2;
        });

        return $routes;
    }

    private function getClassFileRoutes(string $filePath): array
    {
        $routes = [];
        $classes = PhpClassFileParser::parse($filePath);
        foreach($classes as $class) {
            if ($class->hasAttribute(Controller::class)) {
                foreach($class->functions as $function) {
                    if ($function->access == 'public' and $function->hasAttribute(Route::class)) {
                        foreach($function->attributes as $attribute) {
                            $route = str_replace(array('"', "'"), "", $attribute->args);
                            $routes[$route] = [$class->getFullyQualifiedName(), $function->name];
                        }
                    }
                }
            }
        }
        return $routes;
    }
}