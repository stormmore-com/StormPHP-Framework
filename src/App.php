<?php

namespace Stormmore\Framework;

use closure;
use Exception;
use Stormmore\Framework\App\ClassLoader;
use Stormmore\Framework\App\ExceptionMiddleware;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\App\ResponseMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Logger\ILogger;
use Stormmore\Framework\Logger\Logger;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\IO\Request\Request;
use Stormmore\Framework\Mvc\IO\Request\RequestContext;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\Mvc\MvcMiddleware;
use Stormmore\Framework\Mvc\Route\Router;
use Stormmore\Framework\Mvc\View\ViewConfiguration;
use Stormmore\Framework\SourceCode\SourceCode;

class App
{
    private static App|null $instance = null;

    private ILogger $logger;
    private Container $container;
    private SourceCode $sourceCode;
    private ClassLoader $classLoader;
    private Resolver $resolver;
    private Configuration $configuration;
    private AppConfiguration $appConfiguration;
    private ViewConfiguration $viewConfiguration;
    private I18n $i18n;
    private Response $response;
    private Request $request;
    private Router $router;
    private array $middlewares = [];

    public static function create(string $projectDir, string $sourceDir = "", string $cacheDir = ""): App
    {
        self::$instance = new App($projectDir, $sourceDir, $cacheDir);
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
        return $this->appConfiguration;
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

    public function addMiddleware(string $middlewareClassName): void
    {
        $this->middlewares[] = $middlewareClassName;
    }

    public function addRoute(string $key, $value): void
    {
        $this->router->addRoute($key, $value);
    }

    private function __construct(string $projectDir, string $sourceDir = "", string $cacheDir = "")
    {
        $context = new RequestContext();

        $this->configuration = new Configuration();
        $appConfiguration = new AppConfiguration($this->configuration);
        $appConfiguration->setProjectDirectory($projectDir);
        $appConfiguration->setSourceDirectory($sourceDir);
        $appConfiguration->setCacheDirectory($cacheDir);
        $appConfiguration->aliases['@src'] = $appConfiguration->sourceDirectory;

        $this->appConfiguration = $appConfiguration;
        $this->container = new Container();
        $this->resolver = new Resolver($this->container);
        $this->sourceCode = new SourceCode($this->appConfiguration);
        $this->router = new Router($this->sourceCode);
        $this->i18n = new I18n();
        $this->viewConfiguration = new ViewConfiguration();
        $this->classLoader = new ClassLoader($this->sourceCode, $this->appConfiguration);
        $this->response = new Response($context->getCookies());
        $this->request = new Request($context);
        $this->logger = new Logger($appConfiguration);

        $this->container->registerAs($this->logger, ILogger::class);
        $this->container->register($this->configuration);
        $this->container->register($this->appConfiguration);
        $this->container->register($this->sourceCode);
        $this->container->register($this->router);
        $this->container->register(new AppUser());
        $this->container->register($this->i18n);
        $this->container->register($this->viewConfiguration);
        $this->container->register($this->response);
        $this->container->register($this->request);
    }

    public function run(): void
    {
        $environmentFilePath = $this->appConfiguration->projectDirectory . "/env.conf";
        if (file_path_exist($environmentFilePath)) {
            $this->configuration->loadFile($environmentFilePath);
        }

        $this->sourceCode->loadCache();
        $this->classLoader->register();

        $this->runMiddlewares();
    }


    private function runMiddlewares(): void
    {
        $this->middlewares = [ResponseMiddleware::class, ExceptionMiddleware::class, ...$this->middlewares, MvcMiddleware::class];
        $first = $this->getMiddlewareAsCallable(0);
        $first();
    }

    private function getMiddlewareAsCallable(int $i): closure
    {
        if ($i >= count($this->middlewares)) {
            return function() { };
        }
        return function() use ($i) {
            $className = $this->middlewares[$i];
            $middleware = $this->resolver->resolve($className);
            $middleware instanceof IMiddleware or throw new Exception("Class `$className` does not implement IMiddleware interface");
            $middleware->run($this->getMiddlewareAsCallable($i + 1));
        };
    }
}