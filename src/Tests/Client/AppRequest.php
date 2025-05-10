<?php

namespace Stormmore\Framework\Tests\Client;

use Stormmore\Framework\Http\FormData;
use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IRequest;
use Stormmore\Framework\Http\Interfaces\IResponse;

class AppRequest implements IRequest
{
    private string $contentType = "";
    private string $content = "";
    private array $headers = [];
    private array $cookies = [];
    private FormData $formData;

    public function __construct(private readonly string $indexFilePath,
                                private readonly string $method,
                                private string $uri)
    {
    }

    public function withQuery(array $query): IRequest
    {
        $queryString = http_build_query($query);
        $this->uri .= str_contains($this->uri, '?') ? '&' . $queryString : '?' . $queryString;
        return $this;
    }

    public function withHeader(IHeader $header): IRequest
    {
        $this->headers[] = $header;
        return $this;
    }

    public function withCookie(ICookie $cookie): IRequest
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function withForm(FormData $formData): IRequest
    {
        $this->contentType = "multipart/form-data";
        $this->formData = $formData;
        return $this;
    }

    public function withJson(mixed $json): IRequest
    {
        $this->contentType = "application/json";
        $this->content = $json;
        return $this;
    }

    public function withContent(string $type, mixed $content): IRequest
    {
        return $this;
    }

    public function send(): IResponse
    {
        $dir = dirname($this->indexFilePath);
        $filename = basename($this->indexFilePath);

        $headers = array_map(fn($item) => $item->getName() . ":" . $item->getValue(), $this->headers);
        $cookies = array_map(fn($item) => $item->getName() . ":" . $item->getValue(), $this->cookies);
        $args = ["index.php",
            "-r", $this->uri,
            "-method", $this->method,
            "-headers", ...$headers,
            "-cookies", ...$cookies,
            "-print-headers"];

        if ($this->method === "POST") {
            $args[] = '-content-type';
            $args[] = $this->contentType;
            $args[] = '-content';
            $args[] = $this->content;
            if ($this->contentType === "multipart/form-data") {
                $args[] = "-form";
                $args[] = $this->formData;
            }
        }

        $_SERVER["argv"] = $args;

        $cwd = getcwd();
        chdir($dir);
        ob_start();
        if (file_exists($filename)) {
            include($filename);
        }
        $content = ob_get_flush();
        ob_clean();
        chdir($cwd);

        return new AppResponse($content);
    }
}