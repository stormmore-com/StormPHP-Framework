<?php

require  __DIR__ . '/../../vendor/autoload.php';

use Infrastructure\Configuration\LoggerConfiguration;
use Infrastructure\Configuration\AliasConfiguration;
use Infrastructure\Configuration\AppUserConfiguration;
use Infrastructure\Configuration\ErrorConfiguration;
use Infrastructure\Configuration\LocaleConfigure;
use Infrastructure\Configuration\SettingsConfiguration;
use Infrastructure\Middleware\TransactionMiddleware;
use Stormmore\Framework\App;

$app = App::create(
    projectDir: "../",
    sourceDir: "../src",
    cacheDir: "../.cache");

$app->addRoute('/hello', function() {
    return "hello world";
});

$app->addConfiguration(LoggerConfiguration::class);
$app->addConfiguration(AliasConfiguration::class);
$app->addConfiguration(SettingsConfiguration::class);
$app->addConfiguration(LocaleConfigure::class);
$app->addConfiguration(ErrorConfiguration::class);
$app->addConfiguration(AppUserConfiguration::class);

$app->addMiddleware(TransactionMiddleware::class);

$app->run();