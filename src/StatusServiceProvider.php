<?php

namespace SkoreLabs\LaravelStatus;

use Illuminate\Support\ServiceProvider;

class StatusServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');
        }

        if (!class_exists('CreateStatusesTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_statuses_table.php.stub' => database_path("migrations/{$timestamp}_create_statuses_table.php"),
            ], 'migrations');
        }

        $this->publishes([
            __DIR__.'/../config/status.php' => config_path('status.php'),
        ], 'config');
    }
}
