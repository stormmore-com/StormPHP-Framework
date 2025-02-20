<?php

namespace Stormmore\Framework\Template;

use Stormmore\Framework\App;
use Exception;
use Throwable;

class View
{
    private static array $layoutVariables = [];

    private array $bag = [];

    public function __construct(
        private readonly string       $fileName,
        private readonly array|object $data)
    {
    }

    public function __get($key)
    {
        return array_key_exists($key, $this->bag) ? $this->bag[$key] : null;
    }

    public function __set(string $name, $value): void
    {
        $this->bag[$name] = $value;
    }

    /**
     * @throws Exception
     */
    public function toHtml(): string
    {
        $app = App::getInstance();
        $conf = $app->getViewConfiguration();
        foreach($conf->getHelpers() as $helper ) {
            if (!str_ends_with($helper, '.php')) {
                $helper .= '.php';
            }
            import($helper);
        }

        $templateFilePath = resolve_path_alias($this->fileName);
        file_exists($templateFilePath) or throw new Exception("VIEW: $templateFilePath doesn't exist ");

        $data = $this->data;
        if (is_object($data)) {
            $data = get_object_vars($this->data);
        }
        if (is_array($data)) {
            foreach ($data as $name => $value) {
                $this->bag[$name] = $value;
            }
        }

        extract($this->bag, EXTR_OVERWRITE, 'wddx');

        try
        {
            ob_start();
            require $templateFilePath;
            return ob_get_clean();
        }
        catch (Throwable $t) {
            ob_end_clean();
            throw $t;
        }
    }

    public static function getJsScripts(): array
    {
        return array_key_value(self::$layoutVariables, 'js_scripts', []);
    }

    public static function addJsScript(string $script): void
    {
        self::$layoutVariables['js_scripts'][] = $script;
    }

    public static function getCssScripts(): array
    {
        return array_key_value(self::$layoutVariables, 'css_scripts', []);
    }

    public static function addCssScript(string $script): void
    {
        self::$layoutVariables['css_scripts'][] = $script;
    }

    public static function addTitle(string $title): void
    {
        self::$layoutVariables['title'] = $title;
    }

    public static function getTitle(): string
    {
        return array_key_value(self::$layoutVariables, 'title', "");
    }
}