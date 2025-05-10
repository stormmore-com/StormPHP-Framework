<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IRequest;
use Stormmore\Framework\Http\Interfaces\IResponse;

class Request implements IRequest
{
    public function __construct(private string $url, private string $method)
    {
    }

    public function withQuery(array $query): IRequest
    {
        return $this;
        // TODO: Implement withQuery() method.
    }

    public function withHeader(IHeader $header): IRequest
    {
        return $this;
        // TODO: Implement withHeader() method.
    }

    public function withCookie(ICookie $cookie): IRequest
    {
        // TODO: Implement withCookie() method.
        return $this;
    }

    public function withForm(FormData $formData): IRequest
    {
        // TODO: Implement withForm() method.
        return $this;
    }

    public function withJson(mixed $json): IRequest
    {
        // TODO: Implement withJson() method.
        return $this;
    }

    public function withContent(string $contentType, string $content): IRequest
    {
        // TODO: Implement withContent() method.
        return $this;
    }

    public function send(): IResponse
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = new Response($body, $status);

        return $response;
    }
}