<?php

namespace Stormmore\Framework\Http;

class Request implements IRequest
{

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
        // TODO: Implement send() method.
        return $this;
    }
}