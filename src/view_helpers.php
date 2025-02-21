<?php

use Stormmore\Framework\App;
use Stormmore\Framework\Template\IViewComponent;
use Stormmore\Framework\Template\View;

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

function format_date($date, $format = null): string
{
    return _format_date($date, false, $format);
}

function format_datetime($date, $format = null): string
{
    return _format_date($date, true, $format);
}

function format_js_datetime($date): string
{
    if (!$date) return '';
    try {
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }
        return $date->format('Y-m-d H:i:s O');
    } catch (Exception) {
        return "";
    }
}

function _format_date($date, $includeTime = false, $format = null): string
{
    if (!$date) return '';
    if (!is_object($date)) {
        $date = new DateTime($date);
    }

    $i18n = di(I18n::class);
    $date->setTimezone($i18n->culture->timeZone);
    if ($format == null) {
        $format = $includeTime ? $i18n->culture->dateTimeFormat : $i18n->culture->dateFormat;
    }

    return $date->format($format);
}

function format_money($value, $currency = null): string
{
    $i18n = di(I18n::class);
    if (!$currency)
        $currency = $i18n->culture->currency;
    $fmt = numfmt_create($i18n->culture->locale, NumberFormatter::CURRENCY);
    return numfmt_format_currency($fmt, $value, $currency);
}