<?php

namespace Stormmore\Framework\FluentReflection\Class;

use Iterator;
use ReflectionParameter;

class FluentClassParameters implements Iterator
{
    private int $position = 0;
    /**
     * @var ReflectionParameter[]
     */
    private array $parameters = [];

    public function __construct(array $parameters)
    {
        foreach($parameters as $parameter) {
            $this->parameters[] = new FluentClassParameter($parameter);
        }
    }

    public function current(): FluentClassParameter
    {
        return $this->parameters[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return count($this->parameters) - 1 >= $this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->parameters);
    }
}