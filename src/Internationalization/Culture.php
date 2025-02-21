<?php

namespace Stormmore\Framework\Internationalization;

use DateTimeZone;

class Culture
{
    public string $dateFormat = "Y-m-d";
    public string $dateTimeFormat = "Y-m-d H:i";
    public string $currency = "USD";
    public DateTimeZone $timeZone;

    public function __construct()
    {
        $this->timeZone = new DateTimeZone(date_default_timezone_get());
    }
}