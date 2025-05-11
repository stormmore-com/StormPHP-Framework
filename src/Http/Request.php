<?php

namespace Stormmore\Framework\Http;

use CURLFile;
use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IRequest;
use Stormmore\Framework\Http\Interfaces\IResponse;

class Request implements IRequest
{
    private array $headers = [];
    private null|string $content = null;
    private null|string $contentType = null;
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
        $this->headers[$header->getName()] = $header;
        return $this;
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

    public function withContent(string $content, string $contentType = "application/octet-stream"): IRequest
    {
        $this->content = $content;
        $this->contentType = $contentType;
        return $this;
    }

    public function send(): IResponse
    {
        $headers = [];

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($this->method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }

        if ($this->method !== 'GET') {
            if ($this->content) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: $this->contentType"));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->content);
            }

            if ($this->json) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json);
            }

            if ($this->formData) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getFormData());
            }
        }

        if (count($this->headers)) {
            $requestHeaders = [];
            foreach($this->headers as $header) {
                $requestHeaders[] = $header->getName() .  ":" .  $header->getValue();
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        }

        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                if (!str_contains($header, ':')) return $len;
                list($key, $value) = explode(':', $header);

                $headers[strtolower(trim($key))] = trim($value);

                return $len;
            }
        );

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

       return new Response($body, $status, $headers);
    }

    private function getFormData(): array
    {
        $files = $this->formData->getNestedFilesArray();
        array_walk_recursive($files, function (&$file) {
            $file = new CurlFile($file);
        });
        $postData = array_merge($this->formData->getNestedFieldsArray(), $files);
        return $this->flattenArray($postData);
    }

    private function flattenArray(array $data) : array
    {
        if(!is_array($data)) {
            return $data;
        }
        foreach($data as $key => $val) {
            if(is_array($val)) {
                foreach($val as $k => $v) {
                    if(is_array($v)) {
                        $data = array_merge($data, $this->flattenArray(array( "{$key}[{$k}]" => $v)));
                    } else {
                        $data["{$key}[{$k}]"] = $v;
                    }
                }
                unset($data[$key]);
            }
        }
        return $data;
    }
}