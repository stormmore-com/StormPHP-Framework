<?php

namespace Stormmore\Framework;

use Stormmore\Framework\App\ClassLoader;
use Stormmore\Framework\App\ExceptionHandler;
use Stormmore\Framework\App\RequestHandler;
use Stormmore\Framework\App\ResponseHandler;
use Stormmore\Framework\Authentication\IdentityUser;
use Stormmore\Framework\Classes\SourceCode;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Request\Response;
use Stormmore\Framework\Route\Router;
use Stormmore\Framework\Template\ViewConfiguration;
use Throwable;
use Exception;
use closure;

require 'functions.php';

class App
{
    private Container $container;
    private SourceCode $sourceCode;
    private ClassLoader $classLoader;
    private Resolver $resolver;
    private AppConfiguration $configuration;
    private static App|null $instance = null;
    private ViewConfiguration $viewConfiguration;
    private Response $response;
    private Request $request;
    private Router $router;
    private closure|null $addI18nCallback = null;
    private closure|null $addIdentityUserCallback = null;
    private closure|null $addConfigurationCallback = null;
    private closure|null $addViewConfiguration = null;
    private closure|null $beforeRun = null;
    private closure|null $onSuccess = null;
    private closure|null $onFailure = null;

    public static function create(string $sourceDir = "", string $cacheDir = ""): App
    {
        ob_start();

        $appConfiguration = new AppConfiguration();
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

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function beforeRun(callable $callable): void
    {
        $this->beforeRun = $this->resolver->resolveCallable($callable);
    }

    public function onSuccess(callable $callable): void
    {
        $this->onSuccess = $this->resolver->resolveCallable($callable);
    }

    public function onFailure(callable $callable): void
    {
        $this->onFailure = $this->resolver->resolveCallable($callable);
    }

    public function addConfiguration(callable $callable): void
    {
        $this->addConfigurationCallback = $this->resolver->resolveCallable($callable);
    }

    public function addViewConfiguration(callable $callable): void
    {
        $this->addViewConfiguration = $this->resolver->resolveCallable($callable);
    }

    public function addI18n(callable $callable): void
    {
        $this->addI18nCallback = $this->resolver->resolveCallable($callable);
    }

    public function addIdentityUser(callable $callable): void
    {
        $this->addIdentityUserCallback = $this->resolver->resolveCallable($callable);
    }

    public function addRoute(string $key, $value): void
    {
        $this->router->addRoute($key, $value);
    }

    private function __construct(AppConfiguration $configuration)
    {
        $this->container = new Container();
        $this->resolver = new Resolver($this->container);
        $this->router = new Router();
        $this->response = new Response();
        $this->request = new Request($this->resolver);
        $this->configuration = $configuration;
        $this->viewConfiguration = new ViewConfiguration();
        $this->sourceCode = new SourceCode($this->configuration);
        $this->classLoader = new ClassLoader($this->sourceCode, $this->configuration);

        $this->container->register(new IdentityUser());
        $this->container->register(new I18n());
        $this->container->register($this->configuration);
        $this->container->register($this->viewConfiguration);
        $this->container->register($this->response);
        $this->container->register($this->request);
    }

    public function run(): void
    {
        $exceptionHandler = new ExceptionHandler($this->configuration, $this->request, $this->onFailure);
        $requestHandler = new RequestHandler(
            $this->sourceCode,
            $this->configuration,
            $this->request,
            $this->container,
            $this->resolver,
            $this->beforeRun,
            $this->onSuccess);
        $responseHandler = new ResponseHandler();

        try {
            $this->sourceCode->loadCache();
            $this->router->addRoutes($this->sourceCode->routes);

            $this->classLoader->register();

            $this->configureApp();
            $this->configureView();
            $this->configureI18n();
            $this->configureIdentityUser();

            $result = $requestHandler->handle($this->router);
            $responseHandler->handle($this->response, $result);

            ob_flush();
        } catch (Throwable $e) {
            $exceptionHandler->handle($e);
        }
    }

    private function configureApp(): void
    {
        run_callable($this->addConfigurationCallback);
    }

    private function configureView(): void
    {
        run_callable($this->addViewConfiguration);
    }

    private function configureIdentityUser(): void
    {
        if ($this->addIdentityUserCallback != null) {
            $user = run_callable($this->addIdentityUserCallback);
            $user != null or throw new Exception("AddIdentityUser has to return IdentityUser object. Returned NULL.");
            $user instanceof IdentityUser or throw new Exception("AddIdentityUser returned value is not IdentityUser");

            $this->container->registerAs($user, IdentityUser::class);
            $this->container->register($user);
        }
    }

    private function configureI18n(): void
    {
        run_callable($this->addI18nCallback);
    }
}