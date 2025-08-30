<?php

require __DIR__ . '/../../../vendor/autoload.php';

use src\Infrastructure\Authenticator;
use src\Infrastructure\Middleware\CustomMailerMiddleware;
use Stormmore\Framework\App;
use Stormmore\Framework\App\AliasMiddleware;
use Stormmore\Framework\App\ErrorHandlerMiddleware;
use Stormmore\Framework\Configuration\ConfigurationMiddleware;
use Stormmore\Framework\Internationalization\LanguageMiddleware;
use Stormmore\Framework\Mvc\Authentication\AuthenticationMiddleware;
use Stormmore\Framework\Mail\MailerMiddleware;

$app = App::create(directories: [
    'project' => '../',
    'source' => '../src',
    'cache' => '../.cache',
    'logs' => '../.logs',
    'templates' => '../src/templates',
]);

$app->addRoute('/files', '@/src/static/files.php');
$app->addRoute('/hello', function () {
    return "hello world";
});
$app->addMiddleware(ErrorHandlerMiddleware::class, [
    404 => '@templates/errors/404.php',
    500 => '@templates/errors/500.php',
    401 => redirect('/signin'),
    403 => redirect('/signin')
]);
$app->addMiddleware(ConfigurationMiddleware::class, ['@/settings.ini', '@/smtp.ini']);
$app->addMiddleware(MailerMiddleware::class);
$app->addMiddleware(AliasMiddleware::class, [
    '@templates' => "@/src/templates",
    '@mail' => '@/src/lang/mail'
]);
$app->addMiddleware(LanguageMiddleware::class);
$app->addMiddleware(AuthenticationMiddleware::class, Authenticator::class);

$app->run();