<?php

namespace Brendi211\Currency;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CurrencyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCurrency();

        if ($this->app->runningInConsole()) {
            $this->registerResources();
            $this->registerCurrencyCommands();
        }
    }

    public function registerCurrency()
    {
        $this->app->singleton('currency', function ($app) {
            return new Currency(
                $app->config->get('currency', []),
                $app['cache']
            );
        });
    }

    public function registerResources()
    {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__ . '/../config/currency.php' => config_path('currency.php'),
            ], 'config');

            $this->mergeConfigFrom(
                __DIR__ . '/../config/currency.php', 'currency'
            );
        }

        $this->publishes([
            __DIR__ . '/../database/migrations' => base_path('/database/migrations'),
        ], 'migrations');
    }

    public function registerCurrencyCommands()
    {
        $this->commands([
            Console\Cleanup::class,
            Console\Manage::class,
            Console\Update::class,
        ]);
    }

    protected function isLumen(): bool
    {
        return Str::contains($this->app->version(), 'Lumen') === true;
    }
}
