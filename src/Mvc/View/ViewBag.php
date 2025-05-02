<?php

namespace Stormmore\Framework\Mvc\View;

use stdClass;

class ViewBag extends stdClass
{
    public function add(string $name, mixed $value): void
    {
        $this->{$name} = $value;
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return null;
    }
}