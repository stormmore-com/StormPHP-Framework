<?php

require __DIR__ . '/../../../vendor/autoload.php';

use src\Infrastructure\Middleware\AppConfigurationMiddleware;
use src\Infrastructure\Middleware\AppUserConfiguration;
use src\Infrastructure\Middleware\LocaleMiddleware;
use src\Infrastructure\Middleware\SettingsMiddleware;
use src\Infrastructure\Middleware\TransactionMiddleware;
use Stormmore\Framework\App;

$app = App::create(directories: [
    'project' => '../',
    'source' => '../src',
    'cache' => '../.cache',
    'logs' => '../.logs'
]);

$app->addRoute('/hello', function() {
    return "hello world";
});

$app->addMiddleware(AppConfigurationMiddleware::class);
$app->addMiddleware(SettingsMiddleware::class);
$app->addMiddleware(LocaleMiddleware::class);
$app->addMiddleware(AppUserConfiguration::class);
$app->addMiddleware(TransactionMiddleware::class);

$app->run();