<?php

namespace Stormmore\Framework\Mvc\IO\Request\Parameters;

interface IParameters
{
    public function has(array|string $name): bool;
    public function get(array|string $name, $default = null): mixed;

    public function toArray(): array;
}