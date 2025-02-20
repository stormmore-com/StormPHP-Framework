<?php

require '../../vendor/autoload.php';

use Stormmore\Framework\App;

$app = App::create(sourceDir: "../src", cacheDir: "../.cache");

$app->addRoute("/", function() {
    return "<h2>PHP Storm Framework works!</h2> ";
});
$app->addRoute('/hello', function() {
    return "hello world";
});

$app->run();