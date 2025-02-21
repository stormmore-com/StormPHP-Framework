<?php

namespace Stormmore\Framework\Internationalization;

use JsonSerializable;

class Locale implements JsonSerializable
{
    public string $tag;
    public string $languageCode;
    public string $countryCode;

    public function __construct(string $tag = "en-US")
    {
        $this->tag = $tag;
        if (str_contains($this->tag, '-')) {
            list($langCode, $countryCode) = explode('-', $this->tag);
            $this->languageCode = $langCode;
            $this->countryCode = $countryCode;
        } else {
            $this->languageCode = $this->tag;
            $this->countryCode = $this->tag . '-' . strtoupper($this->tag);
        }
    }

    public function equals($obj): bool
    {
        if ($obj instanceof Locale) {
            return $this->tag == $obj->tag or $this->languageCode == $obj->languageCode;
        }
        if (is_string($obj)) {
            return $this->tag == $obj or $this->languageCode == $obj;
        }

        return false;
    }

    public function jsonSerialize(): string
    {
        return $this->tag;
    }
}