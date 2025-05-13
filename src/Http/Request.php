<?php

namespace Stormmore\Framework\Http;

use CURLFile;
use Stormmore\Framework\Http\Exceptions\HttpClientException;
use Stormmore\Framework\Http\Exceptions\HttpCurlException;
use Stormmore\Framework\Http\Exceptions\HttpTimeoutException;
use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IRequest;
use Stormmore\Framework\Http\Interfaces\IResponse;

class Request implements IRequest
{
    private array $cookies = [];
    private array $headers = [];
    private null|string $content = null;
    private null|string $contentType = null;
    private null|object|string $json = null;
    private null|FormData $formData = null;

    public function __construct(private string $url,
                                private string $method,
                                private bool $verifyPeer = true,
                                private string $cert = "",
                                private int $timeout = 0)
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
        $this->cookies[$cookie->getName()] = $cookie;
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
        function_exists('curl_init') or throw new HttpClientException("CURL not installed");

        in_array($this->method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']) or throw new HttpClientException("Invalid request type `{$this->method}`");

        $reqHeaders = [];
        $resHeaders = [];
        $cookies = [];

        if (count($this->headers)) {
            foreach($this->headers as $header) {
                $reqHeaders[] = $header->getName() .  ":" .  $header->getValue();
            }
        }
        if (count($this->cookies)) {
            $cookieHeader = "Cookie:";
            foreach($this->cookies as $cookie) {
                $cookieHeader .= $cookie->getName() . "=" . $cookie->getValue() . ";";
            }
            $reqHeaders[] = $cookieHeader;
        }

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verifyPeer);
        if ($this->cert) {
            file_exists($this->cert) or throw new HttpCurlException("Certificate `$this->cert` not found.");
            curl_setopt($ch, CURLOPT_CAINFO, $this->cert);
        }

        if ($this->timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        }

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

        if ($this->method !== 'GET') {
            if ($this->content) {
                $reqHeaders[] = "Content-Type:" . $this->contentType;
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->content);
            }

            if ($this->json) {
                $reqHeaders[] = 'Content-Type:application/json';
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json);
            }

            if ($this->formData) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getFormData());
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);

        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$resHeaders, &$cookies)
            {
                $len = strlen($header);
                if (!str_contains($header, ':')) return $len;
                list($key, $value) = explode(':', $header);
                $key = strtolower(trim($key));
                $value = trim($value);

                $resHeaders[$key] = $value;
                if ($key == 'set-cookie') {
                    $setCookie = explode(';', $value);
                    if (count($setCookie) > 1) {
                        list($key, $value) = explode('=', $setCookie[0]);
                        if ($key and $value) {
                            $key = trim($key);
                            $value = trim($value);
                            $cookies[$key] = $value;
                        }
                    }
                }
                return $len;
            }
        );

        $body = curl_exec($ch);
        if (curl_errno($ch)) {
            $errno = curl_errno($ch);
            match ($errno) {
                28 => throw new HttpTimeoutException("Timeout expired"),
                60 => throw new HttpClientException("SSL certificate problem (cURL error code 60)"),
                default => throw new HttpCurlException("Unexpected CURL error `$errno` occurred", $errno),
            };
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

       return new Response($body, $status, $resHeaders, $cookies);
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