<?php

namespace Stormmore\Framework\Classes;

use Stormmore\Framework\AppConfiguration;

class SourceCode
{
    private ClassScanner $classScanner;
    private ClassCacheStorage $classCache;
    private RouteScanner $routeScanner;
    private ClassCacheStorage $routeCache;

    public array $classes;
    public array $routes;

    public function __construct(
        private readonly AppConfiguration $configuration)
    {
        $this->classCache = new ClassCacheStorage($this->configuration, 'classes');
        $this->routeCache = new ClassCacheStorage($this->configuration, "routes");
        $this->classScanner = new ClassScanner($this->configuration->sourceDirectory);
        $this->routeScanner = new RouteScanner();
    }

    public function loadCache(): void
    {
        $this->loadClasses();
        $this->loadRoutes();
    }

    public function findFileByFullyQualifiedClassName(string $className): bool|string
    {
        if (isset($this->classes) and array_key_exists($className, $this->classes) and file_exists($this->classes[$className])) {
            return $this->classes[$className];
        }

        $classFileName = $this->configuration->sourceDirectory . "/" . $className . '.php';
        $classFileName = str_replace("\\", "/", $classFileName);
        if (file_exists($classFileName)) {
            return $classFileName;
        }

        return false;
    }

    public function findFullyQualifiedName(string $className): bool|string
    {
        foreach ($this->classes as $fullyQualifiedName => $fileName) {
            if (str_ends_with($fullyQualifiedName, $className)) {
                return $fullyQualifiedName;
            }
        }
        return false;
    }

    public function scanRoutes(): void
    {
        $this->routes = $this->routeScanner->scan($this->classes);
    }

    public function scanFiles(): void
    {
        $this->classes = $this->classScanner->scan();
    }

    public function writeClassCache(): void
    {
        $this->classCache->save($this->classes);
    }

    public function writeRouteCache(): void
    {
        $this->routeCache->save($this->routes);
    }

    private function loadClasses(): void
    {
        if (!$this->classCache->exist()) {
            $classes = $this->classScanner->scan();
            $this->classCache->save($classes);
        }
        $this->classes = $this->classCache->load();
    }

    private function loadRoutes(): void
    {
        if (!$this->routeCache->exist()) {
            $routes = $this->routeScanner->scan($this->classes);
            $this->routeCache->save($routes);
        }

        $this->routes = $this->routeCache->load();
    }
}