<?php

namespace Aejnsn\Postgresify;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider as BaseDatabaseServiceProvider;

class DatabaseServiceProvider extends BaseDatabaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('db.factory', function ($app) {
            $version = $app->version();
            if (substr($version, 0, 5) === "Lumen"){
                $extract = preg_match('/Lumen \(([^()]+)\)/', $app->version(), $match);
                $version = $match[1];
            }
            if (version_compare($version, '5.2.0', '>=')) {
                return new Database\FiveTwo\Connectors\ConnectionFactory($app);
            }
            return new Database\FiveOne\Connectors\ConnectionFactory($app);
        });

        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
    }
}
