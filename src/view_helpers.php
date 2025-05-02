<?php

use Stormmore\Framework\App;
use Stormmore\Framework\Mvc\View\IViewComponent;
use Stormmore\Framework\Mvc\View\View;
use Stormmore\Framework\Mvc\View\ViewBag;

function view(string $templateFileName, array|ViewBag $data = []): View
{
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
    $component = $resolver->resolve($fullyQualifiedComponentName);
    if ($component instanceof IViewComponent) {
        echo $component->view()->toHtml();
    } else {
        throw new Exception("VIEW: @component [$componentName] is not a view component");
    }
}