<?php

use Stormmore\Framework\App;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mvc\IO\Redirect;
use Stormmore\Framework\Mvc\View\IViewComponent;
use Stormmore\Framework\Mvc\View\View;
use Stormmore\Framework\Mvc\View\ViewBag;
use Stormmore\Framework\Std\Path;

if (!function_exists('array_is_list')) {
    function array_is_list(array $array): bool
    {
        $count = count($array);
        for ($i = 0; $i < $count; ++$i) {
            if (!key_exists($i, $array)) {
                return false;
            }
        }
        return true;
    }
}

function is_array_key_value_equal(array $array, string $key, mixed $value): bool
{
    return array_key_exists($key, $array) and $array[$key] == $value;
}

function array_key_value(array $array, string $key, mixed $default): mixed
{
    return array_key_exists($key, $array) ? $array[$key] : $default;
}


function file_path_exist(string $filePath): bool
{
    $filePath = Path::resolve_alias($filePath);
    return file_exists($filePath);
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

function t(string $phrase, ...$args): string
{
    return _args($phrase, $args);
}

function echo_t(string $phrase, ...$args): void
{
    echo _args($phrase, $args);
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
        return Path::concatenate_paths($request->path, $path);
    }
    return Path::concatenate_paths($request->path, $path);
}

function back(string $url = "/", string|bool $success = false, string|bool $failure = false): Redirect
{
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        return redirect($_SERVER['HTTP_REFERER'], $success, $failure);
    }
    return redirect($url, $success, $failure);
}

function redirect(string $url = "/", string|bool $success = false, string|bool $failure = false): Redirect
{
    $response = App::getInstance()->getResponse();
    if ($success) {
        $response->messages->add("success", $success);
    }
    if ($failure) {
        $response->messages->add("failure", $failure);
    }
    return new Redirect($url);
}

function view(string $templateFileName, array|ViewBag $data = []): View
{
    $templateDirectory = App::getInstance()->getAppConfiguration()->templatesDirectory;
    return new View($templateFileName, $data, $templateDirectory);
}

function print_view($templateFileName, array|object $data = []): void
{
    $view = view($templateFileName, $data);
    echo $view->toHtml();
}

function print_component(string $componentName): void
{
    $classLoader = App::getInstance()->getClassLoader();
    $fullyQualifiedComponentName = $classLoader->includeFileByClassName($componentName);
    if (!class_exists($fullyQualifiedComponentName)) {
        throw new Exception("Component $fullyQualifiedComponentName does not exist");
    }
    $resolver = App::getInstance()->getResolver();
    $component = $resolver->resolve($fullyQualifiedComponentName);
    if ($component instanceof IViewComponent) {
        echo $component->view()->toHtml();
    } else {
        throw new Exception("VIEW: @component [$componentName] is not a view component");
    }
}