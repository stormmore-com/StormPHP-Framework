<?php

require  __DIR__ . '/../../vendor/autoload.php';

use Infrastructure\Middleware\AppConfigurationMiddleware;
use Infrastructure\Middleware\AppUserConfiguration;
use Infrastructure\Middleware\LocaleMiddleware;
use Infrastructure\Middleware\SettingsMiddleware;
use Infrastructure\Middleware\TransactionMiddleware;
use Stormmore\Framework\App;

$app = App::create(
    projectDir: "../",
    sourceDir: "../src",
    cacheDir: "../.cache");

$app->addRoute('/hello', function() {
    return "hello world";
});

$app->addMiddleware(AppConfigurationMiddleware::class);
$app->addMiddleware(SettingsMiddleware::class);
$app->addMiddleware(LocaleMiddleware::class);
$app->addMiddleware(AppUserConfiguration::class);
$app->addMiddleware(TransactionMiddleware::class);

$app->run();