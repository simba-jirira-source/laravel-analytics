# Installation

This guide walks through installing Laravel Analytics into a Laravel 12 or 13 application.

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- A relational database supported by Laravel (MySQL, PostgreSQL, SQLite, SQL Server)
- Livewire 4 (installed automatically as a package dependency when you require this package)

## 1. Require the package

```bash
composer require simba-jirira-source/laravel-analytics
```

Laravel discovers `SimbaJirira\LaravelAnalytics\AnalyticsServiceProvider` automatically.

## 2. Publish configuration

```bash
php artisan vendor:publish --tag=analytics-config
```

Review `config/analytics.php`. All features are **disabled by default**.

## 3. Publish and run migrations

Migrations are publishable; they are not loaded silently in production.

```bash
php artisan vendor:publish --tag=analytics-migrations
php artisan migrate
```

Tables created:

| Table | Purpose |
|-------|---------|
| `analytics_visitors` | Unique visitor records |
| `analytics_page_views` | Individual page views |
| `analytics_errors` | Aggregated HTTP error records |
| `analytics_ip_bans` | Exact IP ban entries |

## 4. Enable analytics features

Edit `config/analytics.php`:

```php
'enabled' => true,

'tracking' => [
    'traffic' => true,
    'errors' => true,
],
```

When `enabled` is true:

- `tracking.traffic` registers page-view middleware on the `web` group.
- `tracking.errors` registers HTTP error recording middleware on the `web` group.
- `ip_banning.enabled` (when true) prepends IP-ban enforcement middleware on the `web` group.

No manual middleware registration is required for these defaults.

## 5. Optional — enable the dashboard

The dashboard requires explicit configuration **and** authorization:

```php
'dashboard' => [
    'enabled' => true,
    'path' => 'analytics',
    'authorization' => 'viewAnalyticsDashboard',
    'middleware' => ['web', 'auth'],
],
```

Define the gate (or use an invokable class such as `SimbaJirira\LaravelAnalytics\Support\AllowAuthenticatedDashboardAccess`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewAnalyticsDashboard', fn ($user) => /* your rule */);
```

Routes register only when `dashboard.enabled` is true **and** `dashboard.authorization` is not null.

See [DASHBOARD.md](DASHBOARD.md).

## 6. Optional — publish views

Publish dashboard Blade views to customize markup:

```bash
php artisan vendor:publish --tag=analytics-views
```

## 7. Optional — schedule pruning

The package does not schedule pruning automatically:

```bash
php artisan analytics:prune
```

See [RETENTION.md](RETENTION.md) for scheduling in the host application.

## Publish all resources

```bash
php artisan vendor:publish --tag=analytics
php artisan migrate
```

This publishes config, migrations, views, translations, and assets tagged with `analytics`.

## Workbench (package development)

Contributors working inside this repository can use Orchestra Testbench:

```bash
composer install
composer build
composer serve
```

Run the test suite with:

```bash
composer verify
```

## Troubleshooting

| Symptom | Check |
|---------|-------|
| No page views recorded | `analytics.enabled` and `analytics.tracking.traffic` are true; request uses `web` middleware group; path not in `analytics.ignored` |
| Dashboard 404 | `dashboard.enabled` true and `dashboard.authorization` set |
| Dashboard 403 | Gate / invokable authorization denies the current user |
| Migrations missing | Run `vendor:publish --tag=analytics-migrations` then `migrate` |

## Next steps

- [CONFIGURATION.md](CONFIGURATION.md) — all settings
- [PRIVACY.md](PRIVACY.md) — data collection defaults
- [ARCHITECTURE.md](ARCHITECTURE.md) — internal design
