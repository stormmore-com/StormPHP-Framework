<?php

namespace Stormmore\Framework\FluentReflection\Shared;

class FluentAttributes
{
    private array $fluentAttributes = [];

    public function __construct(private readonly array $attributes)
    {
        foreach($this->attributes as $attribute) {
            $this->fluentAttributes[] = new FluentAttribute($attribute);
        }
    }

    public function hasAny(): bool
    {
        return count($this->fluentAttributes) > 0;
    }

    public function getFirst(): FluentAttribute
    {
        return $this->fluentAttributes[0];
    }

    public function select(callable $closure): array
    {
        $result = [];
        foreach($this->fluentAttributes as $fluentAttribute) {
            $result[] = $closure($fluentAttribute);
        }
        return $result;
    }
}