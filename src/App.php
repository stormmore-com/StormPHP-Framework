<?php

namespace Stormmore\Framework;

use closure;
use Exception;
use Stormmore\Framework\App\ClassLoader;
use Stormmore\Framework\App\ExceptionMiddleware;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\App\MvcMiddleware;
use Stormmore\Framework\App\ResponseMiddleware;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Classes\SourceCode;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mvc\ViewConfiguration;
use Stormmore\Framework\Request\Cookies;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Request\Response;
use Stormmore\Framework\Route\Router;

class App
{
    private Container $container;
    private SourceCode $sourceCode;
    private ClassLoader $classLoader;
    private Resolver $resolver;
    private AppConfiguration $configuration;
    private ViewConfiguration $viewConfiguration;
    private static App|null $instance = null;
    private I18n $i18n;
    private Response $response;
    private Request $request;
    private Router $router;
    private array $middlewareClassNames = [];

    public static function create(string $projectDir = "", string $sourceDir = "", string $cacheDir = ""): App
    {
        $appConfiguration = new AppConfiguration();
        $appConfiguration->setSourceDirectory($sourceDir);
        $appConfiguration->setCacheDirectory($cacheDir);
        $appConfiguration->setProjectDirectory($projectDir);
        $appConfiguration->aliases['@src'] = $appConfiguration->sourceDirectory;

        self::$instance = new App($appConfiguration);
        return self::$instance;
    }

    public static function getInstance(): App
    {
        return self::$instance;
    }

    public function getViewConfiguration(): ViewConfiguration
    {
        return $this->viewConfiguration;
    }

    public function getAppConfiguration(): AppConfiguration
    {
        return $this->configuration;
    }

    public function getResolver(): Resolver
    {
        return $this->resolver;
    }

    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getI18n(): I18n
    {
        return $this->i18n;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param string $callable
     * @return void
     */
    public function add(string $middlewareClassName): void
    {
        $this->middlewareClassNames[] = $middlewareClassName;
    }

    public function addRoute(string $key, $value): void
    {
        $this->router->addRoute($key, $value);
    }

    private function __construct(AppConfiguration $configuration)
    {
        $cookies = new Cookies();

        $this->configuration = $configuration;
        $this->container = new Container();
        $this->resolver = new Resolver($this->container);
        $this->sourceCode = new SourceCode($this->configuration);
        $this->router = new Router($this->sourceCode);
        $this->i18n = new I18n();
        $this->viewConfiguration = new ViewConfiguration();
        $this->classLoader = new ClassLoader($this->sourceCode, $this->configuration);
        $this->response = new Response($cookies);
        $this->request = new Request($cookies, $this->resolver);

        $this->container->register($this->sourceCode);
        $this->container->register($this->router);
        $this->container->register(new AppUser());
        $this->container->register($this->i18n);
        $this->container->register($this->configuration);
        $this->container->register($this->viewConfiguration);
        $this->container->register($this->response);
        $this->container->register($this->request);
    }

    public function run(): void
    {
        $this->sourceCode->loadCache();
        $this->classLoader->register();

        $this->runMiddleware();
    }

    private function runMiddleware(): void
    {
        $this->middlewareClassNames = [ResponseMiddleware::class, ExceptionMiddleware::class, ...$this->middlewareClassNames, MvcMiddleware::class];

        $first = $this->getMiddleware(0);
        $first();
    }

    private function getMiddleware(int $i): closure
    {
        if ($i >= count($this->middlewareClassNames)) {
            return function() { };
        }
        return function() use ($i) {
            $middleware = $this->resolver->resolveObject($this->middlewareClassNames[$i]);
            $middleware instanceof IMiddleware or throw new Exception("Middleware should be callable or class implementing IConfigure");
            $middleware->run($this->getMiddleware($i + 1));
        };
    }
}