<?php

namespace Infrastructure\Settings;

class Settings
{
    public I18n $i18n;

    public function __construct()
    {
        $this->i18n = new I18n();
    }
}