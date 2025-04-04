<?php

namespace Stormmore\Framework\Tests;

use Exception;

class AppRequest
{

    private string $uri = "/";

    private function __construct(private string $indexFilePath)
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
        $output = shell_exec("cd $dir && php $filename -r \"$this->uri\" -print-headers");;

        return new AppResponse($output);
    }
}