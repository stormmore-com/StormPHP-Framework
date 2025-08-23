<?php

namespace Stormmore\Framework\SourceCode;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Std\Path;

class ClassCacheStorage
{
    private string $cacheDirectory;
    private string $cacheFilePath;

    function __construct(AppConfiguration $configuration, $fileName)
    {
        $this->cacheDirectory = $configuration->getCacheDirectory();
        $this->cacheFilePath = Path::concatenate_paths($this->cacheDirectory, $fileName);
    }

    function exist(): bool
    {
        return file_exists($this->cacheFilePath);
    }

    function save(array $var): void
    {
        if (!is_dir($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0777, true);
        }

        $phpFile = '<?php ' . PHP_EOL;
        foreach($var['classes'] as $className => $fileName) {
            $fileName = str_replace("\\", "/", $fileName);
            $phpFile .= '$config["classes"]["' . $className. '"] = "' . $fileName .'";' . PHP_EOL;
        }
        $phpFile .= PHP_EOL;
        foreach($var['routes'] as $route => $defs) {
            $phpFile .= '$config["routes"]["' . $route . '"] = [];' . PHP_EOL;
            foreach($defs as $def) {
                $types = '[';
                $types .= implode(', ', array_map(function ($type) {return '"' . $type . '"';}, $def[2]));
                $types .= ']';
                $phpFile .= '$config["routes"]["' . $route . '"][] = [ "' . $def[0] .'","' . $def[1] .'",' . $types .'];' . PHP_EOL;
            }
        }
        $phpFile .= PHP_EOL;
        foreach($var['commands'] as $className => $fileName) {
            $fileName = str_replace("\\", "/", $fileName);
            $phpFile .= '$config["commands"]["' . $className. '"] = "' . $fileName .'";' . PHP_EOL;
        }
        $phpFile .= PHP_EOL;
        foreach($var['handlers'] as $className => $fileName) {
            $fileName = str_replace("\\", "/", $fileName);
            $phpFile .= '$config["handlers"]["' . $className. '"] = "' . $fileName .'";' . PHP_EOL;
        }
        $phpFile .= PHP_EOL;
        foreach($var['tasks'] as $className => $fileName) {
            $fileName = str_replace("\\", "/", $fileName);
            $phpFile .= '$config["tasks"]["' . $className. '"] = "' . $fileName .'";' . PHP_EOL;
        }
        $phpFile .= PHP_EOL;
        file_put_contents($this->cacheFilePath, $phpFile);
    }

    function load(): array
    {
          $config = [];
          require_once $this->cacheFilePath;
          return $config;;
    }
}