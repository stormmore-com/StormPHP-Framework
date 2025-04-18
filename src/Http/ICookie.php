<?php

namespace Stormmore\Framework\Http;

interface ICookie
{
    public function getName(): string;
    public function getValue(): string;
}