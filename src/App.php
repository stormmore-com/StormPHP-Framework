<?php

namespace Stormmore\Framework;

use closure;
use Exception;
use Stormmore\Framework\App\ClassLoader;
use Stormmore\Framework\App\ExceptionMiddleware;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\App\ResponseMiddleware;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Logger\Configuration;
use Stormmore\Framework\Logger\ILogger;
use Stormmore\Framework\Logger\Logger;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\IO\Cookie\Cookies;
use Stormmore\Framework\Mvc\IO\Request\Request;
use Stormmore\Framework\Mvc\IO\Request\RequestCreator;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\Mvc\MvcMiddleware;
use Stormmore\Framework\Mvc\Route\Router;
use Stormmore\Framework\Mvc\View\ViewConfiguration;
use Stormmore\Framework\SourceCode\SourceCode;

class App
{
    private ILogger $logger;
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
    private array $middlewares = [];
    private array $configurations = [];

    public static function create(string $projectDir, string $sourceDir = "", string $cacheDir = ""): App
    {
        $appConfiguration = new AppConfiguration();
        $appConfiguration->setProjectDirectory($projectDir);
        $appConfiguration->setSourceDirectory($sourceDir);
        $appConfiguration->setCacheDirectory($cacheDir);
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

    public function addMiddleware(string $middlewareClassName): void
    {
        $this->middlewares[] = $middlewareClassName;
    }

    public function addConfiguration(string $configurationClassName): void
    {
        $this->configurations[] = $configurationClassName;
    }

    public function addRoute(string $key, $value): void
    {
        $this->router->addRoute($key, $value);
    }

    private function __construct(AppConfiguration $configuration)
    {
        $cookies = new Cookies();
        $loggerConfiguration = new Configuration();

        $this->configuration = $configuration;
        $this->container = new Container();
        $this->resolver = new Resolver($this->container);
        $this->sourceCode = new SourceCode($this->configuration);
        $this->router = new Router($this->sourceCode);
        $this->i18n = new I18n();
        $this->viewConfiguration = new ViewConfiguration();
        $this->classLoader = new ClassLoader($this->sourceCode, $this->configuration);
        $this->response = new Response($cookies);
        $this->request = RequestCreator::create();
        $this->logger = new Logger($loggerConfiguration);

        $this->container->register($loggerConfiguration);
        $this->container->registerAs($this->logger, ILogger::class);
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

        $this->runConfigurations();
        $this->runMiddlewares();
    }

    private function runConfigurations(): void
    {
        $this->logger->logD("Added " . count($this->configurations) . " configuration(s)");
        foreach($this->configurations as $configurationClassName) {
            $configuration = $this->resolver->resolve($configurationClassName);
            $configuration->configure();
        }
    }

    private function runMiddlewares(): void
    {
        $this->logger->logD("Added " . count($this->middlewares) . " middleware(s)");
        $this->middlewares = [ResponseMiddleware::class, ExceptionMiddleware::class, ...$this->middlewares, MvcMiddleware::class];
        $first = $this->getMiddleware(0);
        $first();
    }

    private function getMiddleware(int $i): closure
    {
        if ($i >= count($this->middlewares)) {
            return function() { };
        }
        return function() use ($i) {
            $className = $this->middlewares[$i];
            $middleware = $this->resolver->resolve($className);
            $middleware instanceof IMiddleware or throw new Exception("Class `$className` does not implement IMiddleware interface");
            $middleware->run($this->getMiddleware($i + 1));
        };
    }
}