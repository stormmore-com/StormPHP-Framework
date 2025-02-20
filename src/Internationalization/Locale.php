<?php

namespace Stormmore\Framework\Internationalization;

use JsonSerializable;

class Locale implements JsonSerializable
{
    public string $tag;
    public string $languageCode;
    public string $countryCode;

    public function __construct($tag)
    {
        $this->tag = $tag;
        if (str_contains($this->tag, '-')) {
            list($langCode,) = explode('-', $this->tag);
            $this->languageCode = $langCode;
            $this->countryCode = strtolower($this->tag);
        } else {
            $this->languageCode = $this->tag;
            $this->countryCode = $this->tag . '-' . $this->tag;
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