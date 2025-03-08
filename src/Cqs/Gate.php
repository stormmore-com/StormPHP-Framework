<?php

namespace Stormmore\Framework\Cqs;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\SourceCode\SourceCode;
use Stormmore\Framework\DependencyInjection\Resolver;
use Exception;

class Gate
{
    private array $history;

    public function __construct(readonly private SourceCode $sourceCode,
                                readonly private AppConfiguration $configuration,
                                readonly private Resolver $resolver)
    {
        $this->history = [];
    }

    public function handle(object $command): void
    {
        $handler = $this->getHandler($command);
        $handler != null or throw new Exception("Gate: Handle for " . get_class($command) . " not found.");
        method_exists($handler, 'handle') or throw new Exception("Gate: handler " . get_class($handler) . " doest not implement handle function");
        $handler->handle($command);
        $this->history[] = get_class($command);
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    private function getHandler(object $command): null|object
    {
        $handler = $this->findHandler($command);
        if ($handler === null and $this->configuration->isDevelopment()) {
            $this->sourceCode->scan();
            $handler = $this->findHandler($command);
            if ($handler !== null) {
                $this->sourceCode->writeCache();
            }
        }
        return $handler;
    }

    private function findHandler(object $command): null|object
    {
        foreach($this->sourceCode->getCommandHandlers() as $fullyQualifiedHandlerName => $commandQualifiedName) {
            if ($commandQualifiedName == get_class($command) and class_exists($fullyQualifiedHandlerName)) {
                return $this->resolver->resolveObject($fullyQualifiedHandlerName);
            }
        }
        return null;
    }
}