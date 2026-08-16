<?php

declare(strict_types=1);

namespace SimbaJirira\LaravelAnalytics;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Livewire\Livewire;
use SimbaJirira\LaravelAnalytics\Console\Commands\AnalyticsIpBanCommand;
use SimbaJirira\LaravelAnalytics\Console\Commands\AnalyticsIpUnbanCommand;
use SimbaJirira\LaravelAnalytics\Console\Commands\AnalyticsPruneCommand;
use SimbaJirira\LaravelAnalytics\Contracts\AnalyticsRecorder;
use SimbaJirira\LaravelAnalytics\Contracts\ErrorRecorder;
use SimbaJirira\LaravelAnalytics\Contracts\VisitorIdentifier;
use SimbaJirira\LaravelAnalytics\Http\Middleware\AuthorizeAnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Http\Middleware\EnforceIpBanMiddleware;
use SimbaJirira\LaravelAnalytics\Http\Middleware\RecordErrorsMiddleware;
use SimbaJirira\LaravelAnalytics\Http\Middleware\TrackTrafficMiddleware;
use SimbaJirira\LaravelAnalytics\Livewire\AnalyticsDashboard;
use SimbaJirira\LaravelAnalytics\Livewire\ErrorDetails;
use SimbaJirira\LaravelAnalytics\Livewire\IpBanManager;
use SimbaJirira\LaravelAnalytics\Livewire\RecentErrors;
use SimbaJirira\LaravelAnalytics\Livewire\StatusBreakdown;
use SimbaJirira\LaravelAnalytics\Livewire\TopPages;
use SimbaJirira\LaravelAnalytics\Livewire\TopReferrers;
use SimbaJirira\LaravelAnalytics\Livewire\TrafficChart;
use SimbaJirira\LaravelAnalytics\Livewire\TrafficOverview;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsDashboardQuery;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsErrorRecorder;
use SimbaJirira\LaravelAnalytics\Services\AnalyticsPruner;
use SimbaJirira\LaravelAnalytics\Services\IpBanService;
use SimbaJirira\LaravelAnalytics\Services\IpUnbanService;
use SimbaJirira\LaravelAnalytics\Services\PageViewRecorder;
use SimbaJirira\LaravelAnalytics\Services\VisitorAnalytics;
use SimbaJirira\LaravelAnalytics\Services\VisitorService;
use SimbaJirira\LaravelAnalytics\Support\AnalyticsHashSalt;
use SimbaJirira\LaravelAnalytics\Support\DashboardAuthorizer;
use SimbaJirira\LaravelAnalytics\Support\DefaultVisitorIdentifier;
use SimbaJirira\LaravelAnalytics\Support\ErrorFingerprintGenerator;
use SimbaJirira\LaravelAnalytics\Support\IpAddressNormalizer;
use SimbaJirira\LaravelAnalytics\Support\IpAddressValidator;
use SimbaJirira\LaravelAnalytics\Support\RequestExclusionChecker;
use SimbaJirira\LaravelAnalytics\Support\SafeExceptionMetadataExtractor;
use SimbaJirira\LaravelAnalytics\Support\SensitiveMessageRedactor;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/analytics.php', 'analytics');

        $this->app->singleton(RequestExclusionChecker::class);
        $this->app->singleton(AnalyticsHashSalt::class);
        $this->app->singleton(IpAddressNormalizer::class);
        $this->app->singleton(IpAddressValidator::class);
        $this->app->singleton(SensitiveMessageRedactor::class);
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

        $this->app->singleton(DashboardAuthorizer::class);
        $this->app->singleton(AnalyticsDashboardQuery::class);
        $this->app->singleton(VisitorService::class);
        $this->app->singleton(VisitorAnalytics::class);
        $this->app->singleton(PageViewRecorder::class);
        $this->app->singleton(AnalyticsErrorRecorder::class);
        $this->app->singleton(AnalyticsPruner::class);
        $this->app->singleton(IpBanService::class);
        $this->app->singleton(IpUnbanService::class);

        $this->app->singleton(AnalyticsRecorder::class, function (Application $app): AnalyticsRecorder {
            $class = config('analytics.analytics_recorder', PageViewRecorder::class);

            return $app->make($class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/dashboard.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'analytics');

        $this->configureLivewire();
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
            AnalyticsIpBanCommand::class,
            AnalyticsIpUnbanCommand::class,
            AnalyticsPruneCommand::class,
        ]);
    }

    protected function configureLivewire(): void
    {
        if (! class_exists(Livewire::class)) {
            return;
        }

        $this->app->booted(function (): void {
            if (! $this->app->bound('livewire')) {
                return;
            }

            foreach ($this->livewireComponents() as $name => $class) {
                Livewire::addComponent($name, class: $class);
            }
        });
    }

    /**
     * @return array<string, class-string<Component>>
     */
    protected function livewireComponents(): array
    {
        return [
            'analytics.analytics-dashboard' => AnalyticsDashboard::class,
            'analytics.traffic-overview' => TrafficOverview::class,
            'analytics.traffic-chart' => TrafficChart::class,
            'analytics.top-pages' => TopPages::class,
            'analytics.top-referrers' => TopReferrers::class,
            'analytics.status-breakdown' => StatusBreakdown::class,
            'analytics.recent-errors' => RecentErrors::class,
            'analytics.error-details' => ErrorDetails::class,
            'analytics.ip-ban-manager' => IpBanManager::class,
        ];
    }

    protected function configureMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('analytics.track-traffic', TrackTrafficMiddleware::class);
        $router->aliasMiddleware('analytics.record-errors', RecordErrorsMiddleware::class);
        $router->aliasMiddleware('analytics.enforce-ip-ban', EnforceIpBanMiddleware::class);
        $router->aliasMiddleware('analytics.dashboard', AuthorizeAnalyticsDashboard::class);

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
