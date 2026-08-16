# Laravel Analytics

First-party, self-hosted application analytics for Laravel.

<p align="center">
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/tests.yml"><img alt="Tests" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/static-analysis.yml"><img alt="Static Analysis" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/static-analysis.yml?branch=main&label=Static%20Analysis&style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/code-style.yml"><img alt="Code Style" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/code-style.yml?branch=main&label=Code%20Style&style=flat-square"></a>
</p>

Track page views, unique visitors, HTTP errors, and optional IP bans from your own database. An optional Livewire 4 dashboard provides KPIs, trends, and management screens when you explicitly enable it.

## Status

Pre-release development. Core analytics features (traffic, visitors, errors, IP banning, retention pruning, and an optional dashboard) are implemented in this repository. No stable Packagist release has been tagged yet.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

These match the `composer.json` constraints. CI tests PHP 8.3–8.5 against Laravel 12 and 13 on Ubuntu and Windows.

## Installation

```bash
composer require simba-jirira-source/laravel-analytics
```

The service provider is registered automatically via Laravel package discovery.

### Publish configuration

```bash
php artisan vendor:publish --tag=analytics-config
```

### Publish and run migrations

Migrations are not loaded automatically. Publish them, then migrate:

```bash
php artisan vendor:publish --tag=analytics-migrations
php artisan migrate
```

### Publish everything (optional)

The shared `analytics` tag publishes config, views, translations, assets, and migrations:

```bash
php artisan vendor:publish --tag=analytics
php artisan migrate
```

Individual publish tags:

| Tag | Contents |
|-----|----------|
| `analytics-config` | `config/analytics.php` |
| `analytics-migrations` | Database migrations |
| `analytics-views` | Blade / Livewire views |
| `analytics-lang` | Translation files |
| `analytics-assets` | Public assets |

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for a full setup walkthrough.

## Quick start

After publishing config and running migrations, enable features explicitly in `config/analytics.php`:

```php
'enabled' => true,

'tracking' => [
    'traffic' => true,
    'errors' => true,
],

'ip_banning' => [
    'enabled' => false, // opt-in; disabled by default
],

'dashboard' => [
    'enabled' => true,
    'authorization' => 'viewAnalyticsDashboard',
    'middleware' => ['web', 'auth'],
],
```

When `enabled` is true and tracking toggles are on, the package registers middleware on the `web` group automatically. You do not need to add middleware aliases manually for the default setup.

Define authorization for the dashboard in your application, for example:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewAnalyticsDashboard', fn ($user) => /* your policy */);
```

Visit `/analytics` (or your configured `dashboard.path`) when the dashboard is enabled and authorized.

## Features

| Feature | Default | Documentation |
|---------|---------|---------------|
| Traffic / page-view tracking | Off | [Configuration](docs/CONFIGURATION.md) |
| Visitor identification | Privacy-aware hashing | [Visitor identification](docs/VISITOR_IDENTIFICATION.md) |
| HTTP error analytics | Off | [Architecture](docs/ARCHITECTURE.md#error-recording) |
| IP banning (exact IPv4/IPv6) | Off | [Configuration](docs/CONFIGURATION.md#ip-banning) |
| Data retention / pruning | 90 days | [Retention](docs/RETENTION.md) |
| Livewire dashboard | Off | [Dashboard](docs/DASHBOARD.md) |

## Artisan commands

| Command | Description |
|---------|-------------|
| `analytics:prune` | Remove records older than the configured retention window |
| `analytics:ip-ban {ip}` | Ban an exact IPv4 or IPv6 address |
| `analytics:ip-unban {ip}` | Remove an active ban |
| `analytics:placeholder` | Skeleton command (development placeholder) |

Pruning is not scheduled automatically. See [docs/RETENTION.md](docs/RETENTION.md).

There is no `analytics:install` command; follow [docs/INSTALLATION.md](docs/INSTALLATION.md) instead.

## Privacy defaults

By default the package:

- does not enable tracking, banning, or the dashboard;
- does not store raw IP addresses;
- hashes visitor identifiers using application-specific salt;
- does not associate authenticated users unless configured;
- excludes dashboard routes from self-tracking.

See [docs/PRIVACY.md](docs/PRIVACY.md) for full details.

> This package provides technical privacy controls but does not itself make an application compliant with any specific privacy law or regulatory framework.

## Configuration overview

All settings live in `config/analytics.php`. See [docs/CONFIGURATION.md](docs/CONFIGURATION.md) for every key.

Extension points (contracts):

- `LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier`
- `LaravelAnalytics\LaravelAnalytics\Contracts\AnalyticsRecorder`
- `LaravelAnalytics\LaravelAnalytics\Contracts\ErrorRecorder`

## Testing

```bash
composer install
composer verify
```

Individual gates:

```bash
composer test          # prepare, PHPStan, Pint, type coverage, Pest
composer analyse       # PHPStan (level 7)
composer lint:check    # Pint
composer test:unit     # Pest
```

## Static analysis

PHPStan level 7 via Larastan. Run `composer run prepare` before `composer analyse` when analysing locally (the `composer test` script does this automatically).

## Code of conduct

See [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## Contributing

Please read [CONTRIBUTING.md](.github/CONTRIBUTING.md) before opening a pull request.

## Security

Report security issues privately. See [SECURITY.md](.github/SECURITY.md).

## Versioning

This project follows [Semantic Versioning](https://semver.org/). APIs may change before `1.0.0`. See [CHANGELOG.md](CHANGELOG.md).

## License

The MIT License. See [LICENSE.md](LICENSE.md).

## Credits

- [simba-jirira-source](https://github.com/simba-jirira-source)
