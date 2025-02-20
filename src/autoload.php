<?php

function storm_framework_autoloader()
{
    $list = array();
    $directory = new RecursiveDirectoryIterator(__DIR__ . "/Framework");
    $iterator = new RecursiveIteratorIterator($directory);
    foreach ($iterator as $filename) {
        if (!$filename->isDir()) {
            $list[] = $filename->getPathname();
            require_once $filename->getPathname();
        }
    }
}
