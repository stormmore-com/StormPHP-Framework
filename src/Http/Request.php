<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IRequest;
use Stormmore\Framework\Http\Interfaces\IResponse;

class Request implements IRequest
{
    private null|object|string $json = null;
    private null|FormData $formData = null;

    public function __construct(private string $url, private string $method)
    {
        $this->method = strtoupper($this->method);
    }

    public function withQuery(array $query): IRequest
    {
        $queryString = http_build_query($query);
        $this->url .= str_contains($this->url, '?') ? '&' . $queryString : '?' . $queryString;
        return $this;
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
        $this->formData = $formData;
        return $this;
    }

    public function withJson(mixed $json): IRequest
    {
        if (is_object($json)) {
            $json = json_encode($json);
        }
        $this->json = $json;
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

        if ($this->method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if ($this->json) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json);
        }

        if ($this->formData) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->formData);
        }


        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = new Response($body, $status);

        return $response;
    }
}