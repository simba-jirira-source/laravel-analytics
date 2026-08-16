<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use LaravelAnalytics\LaravelAnalytics\Console\Commands\AnalyticsIpBanCommand;
use LaravelAnalytics\LaravelAnalytics\Console\Commands\AnalyticsIpUnbanCommand;
use LaravelAnalytics\LaravelAnalytics\Console\Commands\AnalyticsPlaceholderCommand;
use LaravelAnalytics\LaravelAnalytics\Contracts\AnalyticsRecorder;
use LaravelAnalytics\LaravelAnalytics\Contracts\ErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier;
use LaravelAnalytics\LaravelAnalytics\Http\Middleware\EnforceIpBanMiddleware;
use LaravelAnalytics\LaravelAnalytics\Http\Middleware\RecordErrorsMiddleware;
use LaravelAnalytics\LaravelAnalytics\Http\Middleware\TrackTrafficMiddleware;
use LaravelAnalytics\LaravelAnalytics\Services\AnalyticsErrorRecorder;
use LaravelAnalytics\LaravelAnalytics\Services\IpBanService;
use LaravelAnalytics\LaravelAnalytics\Services\IpUnbanService;
use LaravelAnalytics\LaravelAnalytics\Services\PageViewRecorder;
use LaravelAnalytics\LaravelAnalytics\Services\VisitorAnalytics;
use LaravelAnalytics\LaravelAnalytics\Services\VisitorService;
use LaravelAnalytics\LaravelAnalytics\Support\AnalyticsHashSalt;
use LaravelAnalytics\LaravelAnalytics\Support\DefaultVisitorIdentifier;
use LaravelAnalytics\LaravelAnalytics\Support\ErrorFingerprintGenerator;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressNormalizer;
use LaravelAnalytics\LaravelAnalytics\Support\IpAddressValidator;
use LaravelAnalytics\LaravelAnalytics\Support\RequestExclusionChecker;
use LaravelAnalytics\LaravelAnalytics\Support\SafeExceptionMetadataExtractor;

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
        $this->app->singleton(AnalyticsHashSalt::class);
        $this->app->singleton(IpAddressNormalizer::class);
        $this->app->singleton(IpAddressValidator::class);
        $this->app->singleton(ErrorFingerprintGenerator::class);
        $this->app->singleton(SafeExceptionMetadataExtractor::class);

        $this->app->singleton(VisitorIdentifier::class, function (Application $app): VisitorIdentifier {
            $class = config('analytics.visitor_identifier', DefaultVisitorIdentifier::class);

            return $app->make($class);
        });

        $this->app->singleton(ErrorRecorder::class, function (Application $app): ErrorRecorder {
            $class = config('analytics.error_recorder', AnalyticsErrorRecorder::class);

            return $app->make($class);
        });

        $this->app->singleton(VisitorService::class);
        $this->app->singleton(VisitorAnalytics::class);
        $this->app->singleton(PageViewRecorder::class);
        $this->app->singleton(AnalyticsErrorRecorder::class);
        $this->app->singleton(IpBanService::class);
        $this->app->singleton(IpUnbanService::class);

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
            AnalyticsIpBanCommand::class,
            AnalyticsIpUnbanCommand::class,
        ]);
    }

    protected function configureMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('analytics.track-traffic', TrackTrafficMiddleware::class);
        $router->aliasMiddleware('analytics.record-errors', RecordErrorsMiddleware::class);
        $router->aliasMiddleware('analytics.enforce-ip-ban', EnforceIpBanMiddleware::class);

        if (config('analytics.enabled') && config('analytics.ip_banning.enabled')) {
            $router->prependMiddlewareToGroup('web', EnforceIpBanMiddleware::class);
        }

        if (config('analytics.enabled') && config('analytics.tracking.traffic')) {
            $router->pushMiddlewareToGroup('web', TrackTrafficMiddleware::class);
        }

        if (config('analytics.enabled') && config('analytics.tracking.errors')) {
            $router->prependMiddlewareToGroup('web', RecordErrorsMiddleware::class);
        }
    }
}
