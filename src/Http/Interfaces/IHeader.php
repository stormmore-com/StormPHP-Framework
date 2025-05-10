<?php

namespace Stormmore\Framework\Http\Interfaces;

interface IHeader
{
    public function getName(): string;

    public function getValue(): string;
}