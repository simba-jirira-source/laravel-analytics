<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics;

use Illuminate\Support\ServiceProvider;
use LaravelAnalytics\LaravelAnalytics\Console\Commands\AnalyticsPlaceholderCommand;

class LaravelAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/analytics.php', 'analytics');

        $this->app->singleton(LaravelAnalytics::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'analytics');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/analytics.php' => config_path('analytics.php'),
        ], ['analytics', 'analytics-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/analytics'),
        ], ['analytics', 'analytics-views']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/analytics'),
        ], ['analytics', 'analytics-lang']);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/analytics'),
        ], ['analytics', 'analytics-assets']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['analytics', 'analytics-migrations']);

        $this->commands([
            AnalyticsPlaceholderCommand::class,
        ]);
    }
}
