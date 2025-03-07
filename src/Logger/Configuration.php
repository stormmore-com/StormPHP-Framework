<?php

namespace Stormmore\Framework\Logger;

class Configuration
{
    public bool $enabled = true;
    public string $directory = "";
    public string $level = Logger::DEBUG;
}