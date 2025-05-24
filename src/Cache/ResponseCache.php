<?php

namespace Stormmore\Framework\Cache;

use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Mvc\IO\Response;

class ResponseCache
{
    private bool $cacheRequest = false;

    public function __construct(
        private readonly AppConfiguration $configuration,
        private readonly Request          $request,
        private readonly Response         $response,
        private readonly I18n             $i18n
    )
    {
    }

    public function cache(): void
    {
        $this->cacheRequest = true;
    }

    public function read(): object|null
    {
        if (!$this->configuration->cacheEnabled) return null;

        $id = $this->requestToFileName($this->request);
        $cacheFilePath = concatenate_paths($this->cacheDir(), $id);
        if (is_file($cacheFilePath)) {
            $cacheFile = new stdClass();
            $cacheFile->headers = [];
            $cacheFile->body = null;

            $file = fopen($cacheFilePath, "r");
            $cacheFile->createdAt = fgets($file);
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if ($line === "-CONTENT:") {
                    break;
                }
                $header = explode(":", $line);
                $cacheFile->headers[$header[0]] = $header[1];
            }
            while (($line = fgets($file, 1024)) !== false) {
                $cacheFile->body .= $line;
            }
            fclose($file);

            return $cacheFile;
        }

        return null;
    }

    public function write(): void
    {
        if (!$this->configuration->cacheEnabled) return;
        if (!$this->cacheRequest) return;

        $dir = $this->cacheDir();
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if ($this->cacheRequest and $this->response->code == 200) {
            $id = $this->requestToFileName($this->request);
            $filePath = concatenate_paths($this->cacheDir(), $id);

            $file = fopen($filePath, "w");
            fwrite($file, date('m-d-Y H:i:s') . "\n");
            fwrite($file, "Content-Encoding: gzip \n");
            foreach ($this->response->headers as $name => $value) {
                fwrite($file, "$name:$value\n");
            }
            fwrite($file, "-CONTENT:\n");
            fwrite($file, gzencode($this->response->body));
            fclose($file);
        }
    }

    private function outputCache($responseCache): void
    {
        $cachedResponse = $responseCache->read();
        if ($cachedResponse) {
            foreach ($cachedResponse->headers as $name => $value) {
                header("$name: $value");
            }
            if ($this->configuration->isDevelopment()) {
                header("cached: $cachedResponse->createdAt");
            }
            echo $cachedResponse->body;
            die;
        }
    }

    /**
     * glob function is used to delete files
     * https://www.php.net/manual/en/function.glob.php
     * @param string $pattern
     * @return void
     */
    public function delete(string $pattern): void
    {
        $pattern = $this->cacheDir() . "/" . $pattern;
        $files = glob($pattern);
        foreach ($files as $file) {
            while (file_exists($file)) {
                if (!unlink($file)) {
                    usleep(2000);
                }
            }
        }
    }

    private function cacheDir(): string
    {
        return concatenate_paths($this->configuration->getCacheDirectory(), "/responses");
    }

    private function requestToFileName(Request $request): string
    {
        $id = $request->path;
        $id .= "-" . $this->i18n->culture->getLanguage()->primary;
        if ($request->query != '') {
            $id .= "-" . $request->query;
        }
        if ($id != "/") {
            $id = substr($id, 1);
        }
        return str_replace("/", "-", $id);
    }
}