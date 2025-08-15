<?php

require __DIR__ . '/../../../src/autoload.php';

use src\Infrastructure\Middleware\AuthenticationMiddleware;
use src\Infrastructure\Middleware\MailerMiddleware;
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

$app->addRoute('/files', '@/src/static/files.php');
$app->addRoute('/hello', function () {
    return "hello world";
});
$app->addMiddleware(MailerMiddleware::class);
$app->addMiddleware(AliasMiddleware::class, [
    '@templates' => "@/src/templates"
]);
$app->addMiddleware(ConfigurationMiddleware::class, [
    '@/settings.ini']);
$app->addMiddleware(ErrorTemplateMiddleware::class, [
    404 => '@templates/errors/404.php',
    500 => '@templates/errors/500.php',
    'unauthenticated' => redirect('/signin'),
    'unauthorized' => redirect('/signin')
]);
$app->addMiddleware(LanguageMiddleware::class);
$app->addMiddleware(AuthenticationMiddleware::class);

$app->run();