<?php

require '../../vendor/autoload.php';

use Configurables\LocaleConfiguration;
use Stormmore\Framework\App;

$app = App::create(projectDir: "../", sourceDir: "../src", cacheDir: "../.cache");

$app->addConfiguration(LocaleConfiguration::class);

$app->addRoute('/hello', function() {
    return "hello world";
});

$app->run();