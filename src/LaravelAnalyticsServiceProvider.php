<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use LaravelAnalytics\LaravelAnalytics\Console\Commands\AnalyticsPlaceholderCommand;
use LaravelAnalytics\LaravelAnalytics\Contracts\AnalyticsRecorder;
use LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier;
use LaravelAnalytics\LaravelAnalytics\Http\Middleware\TrackTrafficMiddleware;
use LaravelAnalytics\LaravelAnalytics\Services\PageViewRecorder;
use LaravelAnalytics\LaravelAnalytics\Support\DefaultVisitorIdentifier;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;

class LaravelAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/analytics.php', 'analytics');

        $this->app->singleton(LaravelAnalytics::class);

        $this->app->singleton(RequestExclusionChecker::class);

        $this->app->singleton(VisitorIdentifier::class, DefaultVisitorIdentifier::class);

        $this->app->singleton(PageViewRecorder::class);

        $this->app->alias(PageViewRecorder::class, AnalyticsRecorder::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'analytics');

        $this->configureMiddleware();

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

    protected function configureMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('analytics.track-traffic', TrackTrafficMiddleware::class);

        if (config('analytics.enabled') && config('analytics.tracking.traffic')) {
            $router->pushMiddlewareToGroup('web', TrackTrafficMiddleware::class);
        }
    }
}
