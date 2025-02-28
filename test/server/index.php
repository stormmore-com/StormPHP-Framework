<?php

require  __DIR__ . '/../../vendor/autoload.php';

use Stormmore\Framework\App;
use Infrastructure\Middleware\AliasConfiguration;
use Infrastructure\Middleware\AppUserConfiguration;
use Infrastructure\Middleware\ErrorConfiguration;
use Infrastructure\Middleware\LocaleConfiguration;
use Infrastructure\Middleware\SettingsConfiguration;

$app = App::create(projectDir: "../", sourceDir: "../src", cacheDir: "../.cache");
$app->addRoute('/hello', function() {
    return "hello world";
});

$app->add(AliasConfiguration::class);
$app->add(SettingsConfiguration::class);
$app->add(LocaleConfiguration::class);
$app->add(ErrorConfiguration::class);
$app->add(AppUserConfiguration::class);

$app->run();