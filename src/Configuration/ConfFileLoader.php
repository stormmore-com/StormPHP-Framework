<?php

namespace Stormmore\Framework\Configuration;

use Stormmore\Framework\Exceptions\ConfigurationException;

class ConfFileLoader
{
    public const SINGLE_LINE = "one line";
    public const MULTI_LINE = "multiple line";

    private array $lines = [];
    private string $mode = ConfFileLoader::SINGLE_LINE;
    private string $bufferedPropertyName = "";
    private string $bufferedPropertyValue = "";

    public function __construct(private string $path)
    {
    }

    public function parse(): array
    {
        $this->path = resolve_path_alias($this->path);
        file_exists($this->path) or throw new ConfigurationException("File not found $this->path");
        $fileContent = file_get_contents($this->path);
        foreach(explode(PHP_EOL, $fileContent) as $line) {
            if ($this->mode === self::SINGLE_LINE) {
                $this->parseSingleLine($line);
            }
            else if ($this->mode === self::MULTI_LINE) {
                $this->parseMultiline($line);
            }
        }
        $this->mode === self::SINGLE_LINE or throw new ConfigurationException("Multiline property is not closed");

        return $this->lines;
    }

    private function parseSingleLine(string $line): void
    {
        $line = trim($line);
        if (empty($line)) {
            return;
        }
        $pos = strpos($line, '=');
        $pos !== false or throw new ConfigurationException("File `$this->path` is malformed");
        $name = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if (str_starts_with($value, '"""')) {
            $this->mode = ConfFileLoader::MULTI_LINE;
            $this->bufferedPropertyName = $name;
            $this->bufferedPropertyValue = substr($value, 3);
        }
        else {
            $this->lines[$name] = $value;
        }
    }

    private function parseMultiline(string $line): void
    {
        $trimedLine = trim($line);
        if (str_ends_with($trimedLine, '"""')) {
            $this->mode = ConfFileLoader::SINGLE_LINE;
            $this->bufferedPropertyValue .= substr($trimedLine, 0, -3);
            $this->lines[$this->bufferedPropertyName] = $this->bufferedPropertyValue;
        }
        else
        {
            $this->bufferedPropertyValue .= $line;
        }
    }
}