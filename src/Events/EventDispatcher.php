<?php

namespace Stormmore\Framework\Events;

use Exception;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\SourceCode\SourceCode;

class EventDispatcher
{
    private array $history = [];

    public function __construct(readonly private SourceCode $sourceCode,
                                readonly private AppConfiguration $configuration,
                                readonly private Resolver $resolver)
    {
    }

    public function handle(object $event): void
    {
        $eventClassName = get_class($event);
        $handlers = $this->getHandlers($event);
        count($handlers) or throw new Exception("EventDispatcher: Handler for " . $eventClassName . " not found.");
        foreach ($handlers as $handler) {
            method_exists($handler, 'handle') or throw new Exception("EventDispatcher: handler " . $eventClassName . " doest not implement handle function");
            $handler->handle($event);
            $this->addToHistory($event, $handler);
        }
    }

    private function addToHistory(object $event, object $handler): void
    {
        $eventClassName = get_class($event);
        $handlerClassName = get_class($handler);
        if (!array_key_exists($eventClassName, $this->history)) {
            $this->history[$eventClassName] = [];
        }
        $this->history[$eventClassName][] = $handlerClassName;
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    private function getHandlers(object $event): array
    {
        $handlers = $this->findHandlers($event);
        if (!count($handlers) and $this->configuration->isDevelopment()) {
            $this->sourceCode->scan();
            $handlers = $this->findHandlers($event);
            if (count($handlers)) {
                $this->sourceCode->writeCache();
            }
        }
        return $handlers;
    }

    private function findHandlers(object $event): false|array
    {
        $handlers = [];
        foreach($this->sourceCode->getEventHandlers() as $fullyQualifiedHandlerName => $eventQualifiedName) {
            if ($eventQualifiedName == get_class($event)) {
                if (!class_exists($fullyQualifiedHandlerName)) {
                    return array();
                }
                $handlers[] = $this->resolver->resolveObject($fullyQualifiedHandlerName);
            }
        }
        return $handlers;
    }
}