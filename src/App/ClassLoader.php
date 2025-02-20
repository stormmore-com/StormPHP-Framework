<?php

namespace Stormmore\Framework\App;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Classes\SourceCode;

readonly class ClassLoader
{
    public function __construct(
        private SourceCode       $appCode,
        private AppConfiguration $configuration)
    {
    }

    public function register(): void
    {
        spl_autoload_register(function ($className) {
            $this->includeFileByFullyQualifiedClassName($className);
        });
    }

    public function includeFileByFullyQualifiedClassName(string $className): void
    {
        $filePath = $this->appCode->findFileByFullyQualifiedClassName($className);
        if (!$filePath and $this->configuration->isDevelopment()) {
            $this->appCode->scanFiles();
            $filePath = $this->appCode->findFileByFullyQualifiedClassName($className);
            if ($filePath) {
                $this->appCode->writeClassCache();
            }
        }
        if ($filePath) {
            require_once $filePath;
        }
    }

    public function includeFileByClassName(string $className): string
    {
        if (class_exists($className)) {
            return $className;
        }
        $fullyQualifiedComponentName = $this->appCode->findFullyQualifiedName($className);
        $file = $this->appCode->findFileByFullyQualifiedClassName($fullyQualifiedComponentName);
        if ($file) {
            require_once $file;
        }
        if (!$file or !class_exists($fullyQualifiedComponentName) and $this->configuration->isDevelopment()) {
            $this->appCode->scanFiles();
            $fullyQualifiedComponentName = $this->appCode->findFullyQualifiedName($className);
            $file = $this->appCode->findFileByFullyQualifiedClassName($fullyQualifiedComponentName);
            if ($file and class_exists($fullyQualifiedComponentName)) {
                $this->appCode->writeClassCache();
                require_once $file;
            }
        }

        return $fullyQualifiedComponentName;
    }
}