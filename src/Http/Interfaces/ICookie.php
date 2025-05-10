<?php

namespace Stormmore\Framework\Http\Interfaces;

interface ICookie
{
    public function getName(): string;
    public function getValue(): string;
}