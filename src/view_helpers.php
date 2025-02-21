<?php

use Stormmore\Framework\App;
use Stormmore\Framework\Mvc\IViewComponent;
use Stormmore\Framework\Mvc\View;

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