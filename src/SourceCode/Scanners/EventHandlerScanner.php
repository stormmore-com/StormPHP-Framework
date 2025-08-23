<?php

namespace Stormmore\Framework\SourceCode\Scanners;

use Stormmore\Framework\Events\EventHandler;

class EventHandlerScanner
{
    private HandlerScanner $handlerScanner;

    public function __construct()
    {
        $this->handlerScanner = new HandlerScanner(EventHandler::class);
    }

    public function scan(ScannedFileClasses $classes): array
    {
        return $this->handlerScanner->scan($classes->getClasses());
    }
}