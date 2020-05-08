<?php

namespace Omatech\Hexagon;

use Illuminate\Support\ServiceProvider;
use Omatech\Hexagon\Domain\File\WriteFileRepository;
use Omatech\Hexagon\Domain\ServiceProvider\GetServiceProviderRepository;
use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;
use Omatech\Hexagon\Infrastructure\Commands\HexagonalCLI;
use Omatech\Hexagon\Infrastructure\Repositories\File\WriteFile;
use Omatech\Hexagon\Infrastructure\Repositories\ServiceProvider\GetServiceProvider;
use Omatech\Hexagon\Infrastructure\Repositories\String\StringToStudlyCase;
use Omatech\Hexagon\Infrastructure\Repositories\Template\Get;
use Omatech\Hexagon\Infrastructure\Repositories\Template\Instantiate;

class HexagonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/hexagon.php' => config_path('hexagon.php'),
        ], 'hexagon-config');

        $this->publishes([
            __DIR__.'/../resources/templates' => resource_path('vendor/omatech/hexagon/templates'),
        ], 'hexagon-templates');

        if ($this->app->runningInConsole()) {

            // Registering package commands.
             $this->commands([
                 HexagonalCLI::class,
             ]);
        }

        $this->bindRepositories();
    }

    /**
     * Register the application services.
     */
    public function register()
    {

    }

    private function bindRepositories()
    {
        $this->app->bind(
            InstantiateRepository::class,
            Instantiate::class
        );

        $this->app->bind(
            GetRepository::class,
            Get::class
        );

        $this->app->bind(
            StringToStudlyCaseRepository::class,
            StringToStudlyCase::class
        );

        $this->app->bind(
            GetServiceProviderRepository::class,
            GetServiceProvider::class
        );

        $this->app->bind(
            WriteFileRepository::class,
            WriteFile::class
        );
    }
}
