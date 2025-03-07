<?php

namespace Stormmore\Framework\Logger;

interface ILogger
{
    public function logI(string $message): void;
}