<?php

require '../../vendor/autoload.php';

use Stormmore\Framework\App;

$app = App::create(projectDir: "../", sourceDir: "../src", cacheDir: "../.cache");

$app->addRoute('/hello', function() {
    return "hello world";
});

$app->run();