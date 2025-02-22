<?php

namespace Stormmore\Framework;

use closure;
use Exception;
use Stormmore\Framework\App\ClassLoader;
use Stormmore\Framework\App\ExceptionHandler;
use Stormmore\Framework\App\IConfiguration;
use Stormmore\Framework\App\RequestHandler;
use Stormmore\Framework\App\ResponseHandler;
use Stormmore\Framework\Authentication\AppUser;
use Stormmore\Framework\Classes\SourceCode;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\Internationalization\Culture;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Mvc\ViewConfiguration;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Request\Response;
use Stormmore\Framework\Route\Router;
use Throwable;

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
    private array $configurations = [];
    private closure|null $addAppUserClosure = null;
    private closure|null $beforeRunClosure = null;
    private closure|null $onSuccessClosure = null;
    private closure|null $onFailureClosure = null;

    public static function create(string $projectDir = "", string $sourceDir = "", string $cacheDir = ""): App
    {
        ob_start();

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

    public function beforeRun(callable $callable): void
    {
        $this->beforeRunClosure = $this->resolver->resolveCallable($callable);
    }

    public function onSuccess(callable $callable): void
    {
        $this->onSuccessClosure = $this->resolver->resolveCallable($callable);
    }

    public function onFailure(callable $callable): void
    {
        $this->onFailureClosure = $this->resolver->resolveCallable($callable);
    }

    public function addConfiguration(callable|string $callable): void
    {
        $this->configurations[] = $callable;
    }

    public function addAppUser(callable $callable): void
    {
        $this->addAppUserClosure = $this->resolver->resolveCallable($callable);
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
        $this->i18n = new I18n();
        $this->response = new Response();
        $this->request = new Request($this->resolver);
        $this->configuration = $configuration;
        $this->viewConfiguration = new ViewConfiguration();
        $this->sourceCode = new SourceCode($this->configuration);
        $this->classLoader = new ClassLoader($this->sourceCode, $this->configuration);

        $this->container->register(new AppUser());
        $this->container->register($this->i18n);
        $this->container->register($this->configuration);
        $this->container->register($this->viewConfiguration);
        $this->container->register($this->response);
        $this->container->register($this->request);
    }

    public function run(): void
    {
        $exceptionHandler = new ExceptionHandler($this->configuration, $this->request, $this->onFailureClosure);
        $requestHandler = new RequestHandler(
            $this->sourceCode,
            $this->configuration,
            $this->request,
            $this->container,
            $this->resolver,
            $this->beforeRunClosure,
            $this->onSuccessClosure);
        $responseHandler = new ResponseHandler();

        try {
            $this->sourceCode->loadCache();
            $this->router->addRoutes($this->sourceCode->routes);

            $this->classLoader->register();

            $this->configureApp();
            $this->configureAppUser();

            $result = $requestHandler->handle($this->router);
            $responseHandler->handle($this->response, $result);

            ob_flush();
        } catch (Throwable $e) {
            $exceptionHandler->handle($e);
        }
    }

    private function configureApp(): void
    {
        foreach ($this->configurations as $configuration) {
            if (is_callable($configuration)) {
                run_callable($configuration);
            }
            if (is_string($configuration)) {
                $configuration = $this->resolver->resolveObject($configuration);
                $configuration instanceof IConfiguration or throw new Exception("Configuration should be callable or class implementing IConfigure");
                $configuration->configure();
            }
        }
    }

    private function configureAppUser(): void
    {
        if ($this->addAppUserClosure != null) {
            $user = run_callable($this->addAppUserClosure);
            $user != null or throw new Exception("AddAppUser has to return AppUser object. Returned NULL.");
            $user instanceof AppUser or throw new Exception("AddAppUser returned value is not AppUser");

            $this->container->registerAs($user, AppUser::class);
            $this->container->register($user);
        }
    }
}