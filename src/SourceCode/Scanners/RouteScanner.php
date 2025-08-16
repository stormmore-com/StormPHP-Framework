<?php

namespace Stormmore\Framework\SourceCode\Scanners;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Delete;
use Stormmore\Framework\Mvc\Attributes\Get;
use Stormmore\Framework\Mvc\Attributes\Patch;
use Stormmore\Framework\Mvc\Attributes\Post;
use Stormmore\Framework\Mvc\Attributes\Put;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\SourceCode\Parser\Models\PhpAttributes;
use Stormmore\Framework\SourceCode\Parser\PhpClassFileParser;

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
                        $routeAttribute = $function->attributes->getAttribute(Route::class);
                        $routeName = str_replace(array('"', "'"), "", $routeAttribute->args);
                        if (!array_key_exists($routeName, $routes)) {
                            $routes[$routeName] = [];
                        }
                        $types = $this->getHandledRequestType($function->attributes);
                        $routes[$routeName][] = [$class->getFullyQualifiedName(), $function->name, $types];
                    }
                }
            }
        }
        return $routes;
    }

    private function getHandledRequestType(PhpAttributes $attributes): array
    {
        $types = [];
        if ($attributes->hasAttribute(Post::class)) {
            $types[] = 'POST';
        }
        if ($attributes->hasAttribute(Get::class)) {
            $types[] = 'GET';
        }
        if ($attributes->hasAttribute(Delete::class)) {
            $types[] = 'DELETE';
        }
        if ($attributes->hasAttribute(Put::class)) {
            $types[] = 'PUT';
        }
        if ($attributes->hasAttribute(Patch::class)) {
            $types[] = 'PATCH';
        }
        return $types;
    }
}