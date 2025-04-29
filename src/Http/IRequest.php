<?php

namespace Stormmore\Framework\Http;

interface IRequest
{
    public function withQuery(array $query): IRequest;
    public function withHeader(IHeader $header): IRequest;
    public function withCookie(ICookie $cookie): IRequest;
    public function withForm(FormData $formData): IRequest;
    public function withJson(mixed $json): IRequest;
    public function withContent(string $contentType, string $content): IRequest;
    public function send(): IResponse;
}