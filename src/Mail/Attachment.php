<?php

namespace Stormmore\Framework\Mail;

class Attachment
{
    private string $filename;

    public function __construct(private string $filepath, string $filename = "", private string $mimeType = "application/octet-stream")
    {
        $this->filename = $filename;
        if (empty($this->filename)) {
            $this->filename = basename($filepath);
        }
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getContent(): string
    {
        return base64_encode(file_get_contents($this->filepath));
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}