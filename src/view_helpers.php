<?php

use Stormmore\Framework\App;
use Stormmore\Framework\Mvc\IViewComponent;
use Stormmore\Framework\Mvc\View;

function v_helper(string $filename): void
{
    import($filename);
}

function v_include(string $filename, array|object $data = []): void
{
    print_view($filename, $data);
}

function v_title(string $title): void
{
    View::addTitle($title);
}

function print_title(string $defaultTitle): void
{
    $title = View::getTitle();
    if (empty($title)) {
        $title = $defaultTitle;
    }
    echo "<title>$title</title>\n";
}

function print_scripts(): void
{
    foreach(View::getJsScripts() as $js) {
        echo "<script type=\"text/javascript\" src=\"$js\"></script>\n";
    }

    foreach(View::getCssScripts() as $css) {
        echo "<link href=\"$css\" rel=\"stylesheet\">\n";
    }
}

function v_include_js(string|array $url): void
{
    if (is_array($url)) {
        foreach ($url as $jsUrl) {
            View::addJsScript($jsUrl);
        }
    }
    else {
        View::addJsScript($url);
    }
}

function v_include_css(string|array $url): void
{
    if (is_array($url)) {
        foreach ($url as $cssUrl) {
            View::addCssScript($cssUrl);
        }
    }
    else {
        View::addCssScript($url);
    }
}

function view(string $templateFileName, array|object $data = []): View
{
    if (!str_ends_with($templateFileName, '.php')) {
        $templateFileName .= '.php';
    }
    return new View($templateFileName, $data);
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
    $component = $resolver->resolveObject($fullyQualifiedComponentName);
    if ($component instanceof IViewComponent) {
        echo $component->view()->toHtml();
    } else {
        throw new Exception("VIEW: @component [$componentName] is not a view component");
    }
}