<?php

namespace Stormmore\Framework\Mvc;

use Exception;
use Stormmore\Framework\App;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Request\RedirectMessage;
use Stormmore\Framework\Request\Request;
use Throwable;
use stdClass;

class View extends stdClass
{
    public I18n $i18n;
    public Request $request;
    public AppUser $appUser;

    public Html $html;

    private string|null $layoutFileName = null;
    private string|null $htmlMetaTitle = null;
    private array $htmlMetaJsScripts = [];
    private array $htmlMetaCssScripts = [];

    public function __construct(
        private readonly string $fileName,
        private array  $data = [])
    {
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
        $app = App::getInstance();
        $conf = $app->getViewConfiguration();
        foreach ($conf->getHelpers() as $helper) {
            if (!str_ends_with($helper, '.php')) {
                $helper .= '.php';
            }
            import($helper);
        }

        $templateFilePath = resolve_path_alias($this->fileName);
        file_exists($templateFilePath) or throw new Exception("VIEW: `$this->fileName` doesn't exist ");

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
        extract($this->data, EXTR_OVERWRITE, 'wddx');
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