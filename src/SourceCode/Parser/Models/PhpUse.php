<?php

namespace Stormmore\Framework\SourceCode\Parser\Models;

class PhpUse
{
    public function __construct(public string $fullyQualifiedName, public string $as = "")
    {
    }

    public function is(string $className): bool
    {
        if ($this->as == $className) {
            return true;
        }
        $items = explode("\\", $this->fullyQualifiedName);
        if (end($items) == $className) {
            return true;
        }

        return false;
    }
}