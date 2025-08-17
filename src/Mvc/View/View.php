<?php

namespace Stormmore\Framework\Mvc\View;

use Exception;
use stdClass;
use Stormmore\Framework\App;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Std\Path;
use Throwable;

class View extends stdClass
{
    public I18n $i18n;
    public Request $request;
    public AppUser $appUser;

    public Html $html;

    public ViewBag $bag;

    private string|null $layoutFileName = null;
    private string|null $htmlMetaTitle = null;
    private array $htmlMetaJsScripts = [];
    private array $htmlMetaCssScripts = [];

    public function __construct(
        private string $fileName,
        private array|ViewBag $data = [])
    {
        if (!str_ends_with($this->fileName, '.php')) {
            $this->fileName .= '.php';
        }

        if (is_object($this->data)) {
            $this->data = get_object_vars($this->data);
        }
        $this->bag = new ViewBag();
        foreach($this->data as $name => $value) {
            $this->bag->add($name, $value);
        }

        $this->i18n = App::getInstance()->getContainer()->resolve(I18n::class);
        $this->request = App::getInstance()->getContainer()->resolve(Request::class);
        $this->appUser = App::getInstance()->getContainer()->resolve(AppUser::class);
        $this->html = new Html();
    }

    /**
     * @throws Exception
     */
    public function toHtml(): string
    {
        $templateFilePath = Path::resolve_alias($this->fileName);
        file_exists($templateFilePath) or throw new Exception("VIEW: `$this->fileName` doesn't exist ");

        return $this->getTemplateContent($templateFilePath);
    }

    private function getTemplateContent(string $templateFileName): string
    {
        ob_start();
        try {
            $view = $this;
            $bag = $this->bag;
            extract($this->data, EXTR_OVERWRITE, 'wddx');
            require $templateFileName;
            $content = ob_get_clean();
        } catch(Throwable $t) {
            ob_clean();
            throw $t;
        }

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

    public function isset(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function useHelper(string $path): void
    {
        if (!str_ends_with($path, '.php')) {
            $path .= '.php';
        }
        $path = Path::resolve_alias($path);
        require_once $path;
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