<?php

namespace Stormmore\Framework\Internationalization;

use DateTimeZone;

class Culture
{
    public string $locale = "en-US";
    public string $dateFormat = "Y-m-d";
    public string $dateTimeFormat = "Y-m-d H:i";
    public string $currency = "USD";
    public DateTimeZone $timeZone;

    public function getLanguage(): Locale
    {
        return new Locale($this->locale);
    }
}