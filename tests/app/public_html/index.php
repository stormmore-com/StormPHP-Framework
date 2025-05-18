<?php

require __DIR__ . '/../../../vendor/autoload.php';

use src\Infrastructure\Middleware\AppConfigurationMiddleware;
use src\Infrastructure\Middleware\AppUserConfiguration;
use src\Infrastructure\Middleware\LocaleMiddleware;
use src\Infrastructure\Middleware\MailerMiddleware;
use src\Infrastructure\Middleware\TransactionMiddleware;
use Stormmore\Framework\App;
use Stormmore\Framework\Configuration\ConfigurationMiddleware;

$app = App::create(directories: [
    'project' => '../',
    'source' => '../src',
    'cache' => '../.cache',
    'logs' => '../.logs'
]);

$app->addRoute('/hello', function () {
    return "hello world";
});

$app->addMiddleware(AppConfigurationMiddleware::class);
$app->addMiddleware(ConfigurationMiddleware::class, ['@/settings.conf']);
$app->addMiddleware(LocaleMiddleware::class);
$app->addMiddleware(AppUserConfiguration::class);
$app->addMiddleware(TransactionMiddleware::class);

$app->run();