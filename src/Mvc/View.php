<?php

namespace Stormmore\Framework\Mvc;

use Exception;
use Stormmore\Framework\App;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Internationalization\Culture;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Request\Request;
use Throwable;
use stdClass;

class View extends stdClass
{
    private string|null $layoutFileName = null;
    private string|null $htmlMetaTitle = null;
    private array $htmlMetaJsScripts = [];
    private array $htmlMetaCssScripts = [];

    public function __construct(
        private readonly string    $fileName,
        array|object               $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        $container = App::getInstance()->getContainer();
    }

    /**
     * @throws Exception
     */
    public function toHtml(): string
    {
        $app = App::getInstance();
        $conf = $app->getViewConfiguration();
        foreach ($conf->getHelpers() as $helper) {
            if (!str_ends_with($helper, '.php')) {
                $helper .= '.php';
            }
            import($helper);
        }

        $templateFilePath = resolve_path_alias($this->fileName);
        file_exists($templateFilePath) or throw new Exception("VIEW: $templateFilePath doesn't exist ");

        try {
            return $this->getTemplateContent($templateFilePath);
        } catch (Throwable $t) {
            ob_end_clean();
            throw $t;
        }
    }

    private function getTemplateContent(string $templateFileName): string
    {
        ob_start();
        $view = $this;
        require $templateFileName;
        $content = ob_get_clean();
        if ($this->layoutFileName) {
            $layoutView = new View($this->layoutFileName);
            $layoutView->content = $content;
            $layoutView->htmlMetaCssScripts = $this->htmlMetaCssScripts;
            $layoutView->htmlMetaJsScripts = $this->htmlMetaJsScripts;
            $layoutView->htmlMetaTitle = $this->htmlMetaTitle;
            return $layoutView->toHtml();
        }
        return $content;
    }

    public function getRequest(): Request
    {
        return App::getInstance()->getContainer()->resolve(Request::class);
    }
    public function getCulture(): Culture
    {
        return App::getInstance()->getContainer()->resolve(Culture::class);
    }
    public function getLocale(): Locale
    {
        return App::getInstance()->getContainer()->resolve(Locale::class);
    }
    public function getAppUser(): AppUser
    {
        return App::getInstance()->getContainer()->resolve(AppUser::class);
    }

    public function setLayout(string $filename): void
    {
        $this->layoutFileName = $filename;
    }

    public function setTitle(string $title): void
    {
        $this->htmlMetaTitle = $title;
    }

    public function addCssScript(string|array $url): void
    {
        if (is_string($url)) {
            $this->htmlMetaCssScripts[] = $url;
        } else {
            $urls = $url;
            foreach ($urls as $url) {
                $this->htmlMetaCssScripts[] = $url;
            }
        }
    }

    public function addJsScript(string|array $url): void
    {
        if (is_string($url)) {
            $this->htmlMetaJsScripts[] = $url;
        } else {
            $urls = $url;
            foreach ($urls as $url) {
                $this->htmlMetaJsScripts[] = $url;
            }
        }
    }

    public function printJs(): void
    {
        foreach ($this->htmlMetaJsScripts as $js) {
            echo "<script type=\"text/javascript\" src=\"$js\"></script>\n";
        }
    }

    public function printCss(): void
    {
        foreach ($this->htmlMetaCssScripts as $css) {
            echo "<link href=\"$css\" rel=\"stylesheet\">\n";
        }
    }

    public function printTitle(string $title = ""): void
    {
        if ($this->htmlMetaTitle) {
            $title = $this->htmlMetaTitle;
        }
        echo "<title>$title</title>\n";
    }
}