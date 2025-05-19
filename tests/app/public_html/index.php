<?php

require __DIR__ . '/../../../vendor/autoload.php';

use src\Infrastructure\Middleware\AppConfigurationMiddleware;
use src\Infrastructure\Middleware\AppUserConfiguration;
use Stormmore\Framework\App;
use Stormmore\Framework\App\AliasMiddleware;
use Stormmore\Framework\App\ErrorTemplateMiddleware;
use Stormmore\Framework\Configuration\ConfigurationMiddleware;
use Stormmore\Framework\Internationalization\LanguageMiddleware;

$app = App::create(directories: [
    'project' => '../',
    'source' => '../src',
    'cache' => '../.cache',
    'logs' => '../.logs'
]);

$app->addRoute('/hello', function () {
    return "hello world";
});

$app->addMiddleware(AliasMiddleware::class, [
    '@templates' => "@/templates"
]);
$app->addMiddleware(ConfigurationMiddleware::class, [
    '@/settings.conf']);
$app->addMiddleware(ErrorTemplateMiddleware::class, [
    404 => '@templates/errors/404.php',
    500 => '@templates/errors/500.php',
    'unauthenticated' => redirect('/signin'),
    'unauthorized' => redirect('/signin')
]);
$app->addMiddleware(LanguageMiddleware::class);
$app->addMiddleware(AppUserConfiguration::class);

$app->run();