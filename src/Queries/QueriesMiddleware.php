<?php

namespace Stormmore\Framework\Queries;

use closure;
use Exception;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Queries\Connection;
use Stormmore\Queries\ConnectionFactory;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\StormQueries;


class QueriesMiddleware implements IMiddleware
{
    public function __construct(private Configuration $configuration, private Container $container)
    {
    }

    public function run(closure $next, array $options = []): void
    {
        class_exists('Stormmore\Queries\ConnectionFactory') or throw new Exception("Install StormQueries library.");
        $this->configuration->has('database.connection') and
         $this->configuration->has('database.user') and
         $this->configuration->has('database.password') or throw new Exception("Database Connection Failed.");

        $connectionString = $this->configuration->get('database.connection');
        $user = $this->configuration->get('database.user');
        $password = $this->configuration->get('database.password');

        $connection = ConnectionFactory::createFromString($connectionString, $user, $password);

        $this->container->registerAs($connection, 'Stormmore\Queries\IConnection');

        $next();
    }
}