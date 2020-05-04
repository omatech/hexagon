<?php

namespace Omatech\Hexagon;

use Illuminate\Support\ServiceProvider;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Infrastructure\Commands\HexagonalCLI;
use Omatech\Hexagon\Infrastructure\Commands\HexagonalCLI2;
use Omatech\Hexagon\Infrastructure\Repositories\File\Instantiate;

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

        $this->app->bind(
            InstantiateRepository::class,
            Instantiate::class
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {

    }
}
