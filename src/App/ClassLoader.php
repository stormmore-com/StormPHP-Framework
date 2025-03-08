<?php

namespace Stormmore\Framework\App;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\SourceCode\SourceCode;

readonly class ClassLoader
{
    public function __construct(
        private SourceCode       $sourceCode,
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
        $filePath = $this->sourceCode->findFileByFullyQualifiedClassName($className);
        if (!$filePath and $this->configuration->isDevelopment()) {
            $this->sourceCode->scan();
            $filePath = $this->sourceCode->findFileByFullyQualifiedClassName($className);
            if ($filePath) {
                $this->sourceCode->writeCache();
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
        $fullyQualifiedComponentName = $this->sourceCode->findFullyQualifiedName($className);
        $file = $this->sourceCode->findFileByFullyQualifiedClassName($fullyQualifiedComponentName);
        if ($file) {
            require_once $file;
        }
        if (!$file or !class_exists($fullyQualifiedComponentName) and $this->configuration->isDevelopment()) {
            $this->sourceCode->scan();
            $fullyQualifiedComponentName = $this->sourceCode->findFullyQualifiedName($className);
            $file = $this->sourceCode->findFileByFullyQualifiedClassName($fullyQualifiedComponentName);
            if ($file and class_exists($fullyQualifiedComponentName)) {
                $this->sourceCode->writeCache();
                require_once $file;
            }
        }

        return $fullyQualifiedComponentName;
    }
}