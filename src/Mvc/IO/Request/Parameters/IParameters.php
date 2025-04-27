<?php

namespace Stormmore\Framework\Mvc\IO\Request\Parameters;

interface IParameters
{
    public function has(string $name): bool;
    public function get(string $name): mixed;

    public function toArray(): array;
}