<?php

namespace Stormmore\Framework\Tests;

use Exception;

class AppRequest
{
    private string $uri = "/";

    private function __construct(private readonly string $indexFilePath)
    {
    }

    public static function create(string $indexFilePath)
    {
        file_exists($indexFilePath) or throw new Exception("Storm app index file not found `$indexFilePath`");
        return new AppRequest($indexFilePath);
    }

    public function setUrl(string $uri): AppRequest
    {
        $this->uri = $uri;
        return $this;
    }

    public function run(): AppResponse
    {
        $dir = dirname($this->indexFilePath);
        $filename = basename($this->indexFilePath);

        $_SERVER["argv"] = ["index.php", "-r", $this->uri, "-print-headers"];

        $cwd = getcwd();
        chdir($dir);
        ob_start();
        if (file_exists($filename)) {
            include($filename);
        }
        $content = ob_get_flush();
        ob_end_clean();
        chdir($cwd);

        return new AppResponse($content);
    }
}