<?php

namespace Stormmore\Framework\Http\Interfaces;

use Stormmore\Framework\Http\FormData;

interface IRequest
{
    public function withQuery(array $query): IRequest;
    public function withHeader(IHeader $header): IRequest;
    public function withCookie(ICookie $cookie): IRequest;
    public function withForm(FormData $formData): IRequest;
    public function withJson(mixed $json): IRequest;
    public function withContent(string $content, string $contentType): IRequest;
    public function send(): IResponse;
}