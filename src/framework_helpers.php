<?php

use Random\Randomizer;
use Stormmore\Framework\App;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mvc\IO\Redirect;

if (!function_exists('array_is_list')) {
    function array_is_list(array $array): bool
    {
        $count = count($array);
        for($i = 0; $i < $count; ++$i) {
            if (!key_exists($i, $array)) {
                return false;
            }
        }
        return true;
    }
}

function is_cli(): bool
{
    return php_sapi_name() === 'cli';
}

function resolve_path_alias(string $templatePath): string
{
    $configuration = App::getInstance()->getAppConfiguration();
    $appDirectory = $configuration->projectDirectory;
    $aliases = $configuration->aliases;
    if (str_starts_with($templatePath, "@/")) {
        return str_replace("@", $appDirectory, $templatePath);
    } else if (str_starts_with($templatePath, '@')) {
        $firstSeparator = strpos($templatePath, "/");
        if ($firstSeparator) {
            $alias = substr($templatePath, 0, $firstSeparator);
            $path = substr($templatePath, $firstSeparator);
        } else {
            $alias = $templatePath;
            $path = '';
        }
        if (!array_key_exists($alias, $aliases)) { return false;}
        $aliasPath = $aliases[$alias];
        if (str_starts_with($aliasPath, '@')) {
            $aliasPath = resolve_path_alias($aliasPath);
        }

        $templatePath = $aliasPath . $path;
    }

    return $templatePath;
}

function file_path_exist(string $filePath): bool
{
    $filePath = resolve_path_alias($filePath);
    return file_exists($filePath);
}

function is_array_key_value_equal(array $array, string $key, mixed $value): bool
{
    return array_key_exists($key, $array) and $array[$key] == $value;
}

function array_key_value(array $array, string $key, mixed $default): mixed
{
    return array_key_exists($key, $array) ? $array[$key] : $default;
}

function split_file_name_and_ext(string $filename): array
{
    $lastDotPos = strrpos($filename, '.');
    if ($lastDotPos !== false and $lastDotPos > 0 and strlen($filename) - $lastDotPos < 5) {
        $name = substr($filename, 0, $lastDotPos);
        $ext = substr($filename, $lastDotPos + 1);
        return [$name, $ext];
    }
    return [$filename, ''];
}

function concatenate_paths(string ...$paths): string
{
    $path = '';
    for ($i = 0; $i < count($paths); $i++) {
        $element = $paths[$i];
        if ($i < count($paths) - 1 and !str_ends_with($element, "/")) {
            $element .= "/";
        }
        if (str_ends_with($path, "/") and str_starts_with($element, "/")) {
            $element = substr($element, 1);
        }
        $path .= $element;
    }
    return $path;
}

/**
 * @param int $length length with or without extension. Default 64. Optional.
 * @param string $extension file extension. Optional.
 * @param string $directory to check whether unique file exist or not. Optional
 * @return string generated unique file name
 */
function gen_unique_file_name(int $length = 64, string $extension = '', string $directory = ''): string
{
    $filename = '';
    if (!empty($extension)) {
        $length = $length - strlen($extension) - 1;
    }
    do {
        $randomizer = new Randomizer();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $filename .= $characters[$randomizer->getInt(0, $charactersLength - 1)];
        }
        if (!empty($extension)) {
            $filename .= '.' . $extension;
        }
    } while (!empty($directory) and file_exists($directory . "/" . $filename));

    return $filename;
}

function none_empty_explode($delimiter, $string, $limit = PHP_INT_MAX): array
{
    if (str_starts_with($string, $delimiter)) {
        $string = substr($string, 1);
    }
    if (str_ends_with($string, $delimiter)) {
        $string = substr($string, 0, -1);
    }
    return explode($delimiter, $string, $limit);
}

function di(string|null $key = null): mixed
{
    $container = App::getInstance()->getContainer();
    if ($key == null)
        return $container;
    return $container->resolve($key);
}

function _(string $phrase, ...$args): string
{
    return _args($phrase, $args);
}

function _p(string $phrase, ...$args): void
{
    echo _args($phrase, $args);
}

function _quantity(string $phrase, int $num, ...$args): string
{
    if ($num == 1) {
        return _args($phrase . "_singular", $args);
    }
    return _args($phrase . "_plural", $args);
}

function _args(string $phrase, array $args): string
{
    $container = App::getInstance()->getContainer();
    $i18n = $container->resolve(I18n::class);
    $translatedPhrase = $i18n->translate($phrase);
    if (count($args)) {
        return vsprintf($translatedPhrase, $args);
    }

    return $translatedPhrase;
}

/**
 * @throws Exception
 */
function import(string $file): void
{
    $file = resolve_path_alias($file);
    if (str_ends_with($file, "/*")) {
        $dir = str_replace("/*", "", $file);
        $files = scandir($dir);
        foreach ($files as $file) {
            if (str_ends_with($file, ".php")) {
                require_once($dir . "/" . $file);
            }
        }
    } else {
        file_exists($file) or throw new Exception("IMPORT: file [$file] doesn't exists");
        require_once($file);
    }
}

function url($path, $args = array()): string
{
    $request = App::getInstance()->getRequest();
    if (count($args)) {
        $query = http_build_query($args);
        if (!empty($query))
            $path = $path . "?" . $query;
    }
    $pos = strrpos($path, '.');
    if ($pos !== false and strlen($path) - $pos < 5) {
        return concatenate_paths($request->uri, $path);
    }
    return concatenate_paths($request->uri, $path);
}

function back(string $url = "/"): Redirect
{
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        return redirect($_SERVER['HTTP_REFERER']);
    }

    return redirect($url);
}

function redirect(string $url = "/"): Redirect
{
    return new Redirect($url);
}