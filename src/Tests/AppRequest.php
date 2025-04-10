<?php

namespace Stormmore\Framework\Tests;

use Exception;

class AppRequest
{
    private string $uri = "/";

    private array $headers = [];

    private array $cookies = [];

    private string $method = "GET";

    private function __construct(private readonly string $indexFilePath)
    {
    }

    public static function create(string $indexFilePath)
    {
        file_exists($indexFilePath) or throw new Exception("Storm app index file not found `$indexFilePath`");
        return new AppRequest($indexFilePath);
    }

    public function request(string $method, string $uri): AppRequest
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        return $this;
    }

    public function withHeader(AppRequestHeader $header): AppRequest
    {
        $this->headers[] = $header;
        return $this;
    }

    public function withCookie(AppCookie $cookie): AppRequest
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function run(): AppResponse
    {
        $dir = dirname($this->indexFilePath);
        $filename = basename($this->indexFilePath);

        $headers = array_map(fn($item) => $item->name . ":" . $item->value, $this->headers);
        $cookies = array_map(fn($item) => $item->name . ":" . $item->value, $this->cookies);
        $_SERVER["argv"] = ["index.php",
            "-r", $this->uri,
            "-method", $this->method,
            "-headers", ...$headers,
            "-cookies", ...$cookies,
            "-print-headers"];

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