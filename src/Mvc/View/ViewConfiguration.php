<?php

namespace Stormmore\Framework\Mvc\View;

class ViewConfiguration
{
    private array $helpers = [];

    public function addHelper(string $filename): void
    {
        $this->helpers[] = $filename;
    }

    public function getHelpers(): array
    {
        return $this->helpers;
    }
}