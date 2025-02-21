<?php

require '../../vendor/autoload.php';

use Stormmore\Framework\App;
use Infrastructure\Configurations\LocaleConfiguration;
use Infrastructure\Configurations\SettingsConfiguration;

$app = App::create(projectDir: "../", sourceDir: "../src", cacheDir: "../.cache");

$app->addConfiguration(SettingsConfiguration::class);
$app->addConfiguration(LocaleConfiguration::class);

$app->addRoute('/hello', function() {
    return "hello world";
});

$app->run();