<?php

namespace Stormmore\Framework\Cqs;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Classes\SourceCode;
use Stormmore\Framework\DependencyInjection\Resolver;
use Exception;

class Gate
{
    private array $history = [];

    public function __construct(private SourceCode $sourceCode, private AppConfiguration $configuration, private Resolver $resolver)
    {
    }

    public function handle(object $command): void
    {
        $handler = $this->getCommandHandler($command);
        $handler != null or throw new Exception("Gate: Handle for " . get_class($command) . " not found.");
        method_exists($handler, 'handle') or throw new Exception("Gate: handler " . get_class($handler) . " doest not implement handle function");
        $handler->handle($command);
        $this->history[] = get_class($command);
    }

    public function getGateHistory(): array
    {
        return $this->history;
    }

    private function getCommandHandler(object $command): null|object
    {
        $handler = $this->findCommandHandler($command);
        if ($handler === null and $this->configuration->isDevelopment()) {
            $this->sourceCode->scan();
            $handler = $this->findCommandHandler($command);
            if ($handler !== null) {
                $this->sourceCode->writeCache();
            }
        }
        return $handler;
    }

    private function findCommandHandler(object $command): null|object
    {
        foreach($this->sourceCode->getCommandHandlers() as $fullyQualifiedHandlerName => $commandQualifiedName) {
            if ($commandQualifiedName == get_class($command) and class_exists($fullyQualifiedHandlerName)) {
                return $this->resolver->resolveObject($fullyQualifiedHandlerName);
            }
        }
        return null;
    }
}