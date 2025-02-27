<?php

require  __DIR__ . '/../../vendor/autoload.php';

use Stormmore\Framework\App;
use Infrastructure\Configurations\AliasConfiguration;
use Infrastructure\Configurations\AppUserConfiguration;
use Infrastructure\Configurations\ErrorConfiguration;
use Infrastructure\Configurations\LocaleConfiguration;
use Infrastructure\Configurations\SettingsConfiguration;

$app = App::create(projectDir: "../", sourceDir: "../src", cacheDir: "../.cache");
$app->addRoute('/hello', function() {
    return "hello world";
});

$app->add(AliasConfiguration::class);
$app->add(SettingsConfiguration::class);
$app->add(LocaleConfiguration::class);
$app->add(ErrorConfiguration::class);
$app->add(AppUserConfiguration::class);

$app->run();