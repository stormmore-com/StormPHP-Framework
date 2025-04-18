<?php

namespace Stormmore\Framework\Http;

interface IHeader
{
    public function getName(): string;

    public function getValue(): string;
}