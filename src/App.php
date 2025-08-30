<?php

namespace Stormmore\Framework;

use closure;
use Stormmore\Framework\App\ClassLoader;
use Stormmore\Framework\App\ExceptionMiddleware;
use Stormmore\Framework\App\MiddlewareChain;
use Stormmore\Framework\App\RequestContext;
use Stormmore\Framework\App\ResponseMiddleware;
use Stormmore\Framework\Cli\CliMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Logger\ILogger;
use Stormmore\Framework\Logger\Logger;
use Stormmore\Framework\Mvc\Authentication\AppUser;
use Stormmore\Framework\Mvc\IO\Request;
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
    private I18n $i18n;
    private Response $response;
    private Request $request;
    private Router $router;
    private MiddlewareChain $middlewareChain;

    public static function create(array $directories = []): App
    {
        self::$instance = new App($directories);
        return self::$instance;
    }

    public static function getInstance(): App
    {
        return self::$instance;
    }

    public function getAppConfiguration(): AppConfiguration
    {
        return $this->appConfiguration;
    }

    public function getConfiguration(): Configuration
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

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function addMiddleware(string $middlewareClassName, closure|string|array $options = []): void
    {
        $this->middlewareChain->add($middlewareClassName, $options);
    }

    public function addRoute(string $key, callable|string $value): void
    {
        $this->router->addRoute($key, $value);
    }

    private function __construct(array $directories = [])
    {
        $context = new RequestContext();

        $this->configuration = new Configuration();
        $appConfiguration = new AppConfiguration($this->configuration);
        $appConfiguration->setDirectories($directories);
        $appConfiguration->aliases['@src'] = $appConfiguration->sourceDirectory;

        $this->appConfiguration = $appConfiguration;
        $this->container = new Container();
        $this->resolver = new Resolver($this->container);
        $this->sourceCode = new SourceCode($this->appConfiguration);
        $this->router = new Router($this->sourceCode);
        $this->i18n = new I18n();

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
        $this->container->register($this->response);
        $this->container->register($this->request);

        $this->middlewareChain = new MiddlewareChain($this->resolver);
        $this->middlewareChain
            ->add(ResponseMiddleware::class)
            ->add(ExceptionMiddleware::class)
            ->add(CliMiddleware::class);
    }

    public function run(): void
    {
        $environmentFilePath = $this->appConfiguration->projectDirectory . "/env.ini";
        if (file_path_exist($environmentFilePath)) {
            $this->configuration->loadFile($environmentFilePath);
        }

        $this->sourceCode->loadCache();
        $this->classLoader->register();

        $this->middlewareChain->add(MvcMiddleware::class);

        $this->middlewareChain->run();
    }
}