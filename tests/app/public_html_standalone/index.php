<?php

require __DIR__ . '/../../../src/autoload.php';

use src\Infrastructure\Authenticator;
use src\Infrastructure\Middleware\Authentication2Middleware;
use src\Infrastructure\Middleware\CustomMailerMiddleware;
use Stormmore\Framework\App;
use Stormmore\Framework\App\AliasMiddleware;
use Stormmore\Framework\App\ErrorHandlerMiddleware;
use Stormmore\Framework\Configuration\ConfigurationMiddleware;
use Stormmore\Framework\Internationalization\LanguageMiddleware;
use Stormmore\Framework\Mvc\Authentication\AuthenticationMiddleware;

$app = App::create(directories: [
    'project' => '../',
    'source' => '../src',
    'cache' => '../.cache',
    'logs' => '../.logs'
]);

$app->addRoute('/files', '@/src/static/files.php');
$app->addRoute('/hello', function () {
    return "hello world";
});
$app->addMiddleware(CustomMailerMiddleware::class);
$app->addMiddleware(AliasMiddleware::class, [
    '@templates' => "@/src/templates"
]);
$app->addMiddleware(ConfigurationMiddleware::class, ['@/settings.ini']);
$app->addMiddleware(ErrorHandlerMiddleware::class, [
    404 => '@templates/errors/404.php',
    500 => '@templates/errors/500.php',
    'unauthenticated' => redirect('/signin'),
    'unauthorized' => redirect('/signin')
]);
$app->addMiddleware(LanguageMiddleware::class);
$app->addMiddleware(AuthenticationMiddleware::class, Authenticator::class);

$app->run();