# Laravel Analytics

First-party, self-hosted application analytics for Laravel.

<p align="center">
    <a href="https://packagist.org/packages/simba-jirira-source/laravel-analytics"><img alt="Latest Version" src="https://img.shields.io/packagist/v/simba-jirira-source/laravel-analytics?style=flat-square"></a>
    <a href="https://packagist.org/packages/simba-jirira-source/laravel-analytics"><img alt="License" src="https://img.shields.io/packagist/l/simba-jirira-source/laravel-analytics?style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/tests.yml"><img alt="Tests" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/static-analysis.yml"><img alt="Static Analysis" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/static-analysis.yml?branch=main&label=Static%20Analysis&style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/code-style.yml"><img alt="Code Style" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/code-style.yml?branch=main&label=Code%20Style&style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/database.yml"><img alt="Database" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/database.yml?branch=main&label=Database&style=flat-square"></a>
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions/workflows/security.yml"><img alt="Security" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/security.yml?branch=main&label=Security&style=flat-square"></a>
</p>

Track page views, unique visitors, HTTP errors, and optional IP bans in your application's own database. Enable an optional Livewire 4 dashboard when you want KPIs, trends, and management screens inside your Laravel app — without routing analytics data through a third-party platform by default.

<!-- Screenshot: uncomment after capturing docs/images/dashboard-overview.png
![Analytics dashboard overview](docs/images/dashboard-overview.png)
-->

See [docs/SCREENSHOTS.md](docs/SCREENSHOTS.md) for recommended dashboard screenshots.

## Why Laravel Analytics?

Laravel Analytics provides first-party analytics designed specifically for Laravel applications. It is intended for teams who want analytics data stored alongside their application rather than depending entirely on an external analytics service.

- Analytics data stays in the application's own database
- Tracking is disabled by default
- Raw IP storage is disabled by default
- Privacy-aware visitor hashing
- Laravel-native package architecture (service provider, middleware, contracts, Artisan commands)
- HTTP error analytics alongside traffic metrics
- Optional exact IPv4/IPv6 access controls
- Optional Livewire 4 dashboard with authorization gates
- Configurable retention and pruning
- SQLite, MySQL, and PostgreSQL tested in CI
- Laravel 12 and 13 tested in CI
- PHP 8.3–8.5 tested in CI

Pre-1.0 development. See [GitHub Releases](https://github.com/simba-jirira-source/laravel-analytics/releases) and [CHANGELOG.md](CHANGELOG.md) for released versions.

## Key Features

| Capability | Default | Notes |
|------------|---------|-------|
| Page views | Off | Middleware-based traffic tracking |
| Unique visitors | Privacy-aware hashing | Hashed identifiers; raw IP off by default |
| HTTP error analytics | Off | Fingerprinted error aggregation |
| Exact IPv4/IPv6 bans | Off | Optional middleware enforcement |
| Retention / pruning | 90 days | `analytics:prune` command |
| Livewire dashboard | Off | Gate or invokable authorization required |
| Cross-database support | SQLite, MySQL, PostgreSQL | Integration tests in CI |
| Replaceable contracts | Configurable | Visitor, traffic, and error recorders |
| Composer security auditing | In CI and `composer verify` | `security.yml` workflow |

## How It Differs

| Capability | Laravel Analytics | External analytics integration |
|------------|-------------------|--------------------------------|
| Data stored in application database | Yes | Usually no |
| Self-hosted analytics data | Yes | Depends on provider |
| Page views | Yes | Yes |
| Unique visitors | Yes | Yes |
| HTTP error analytics | Yes | Usually separate tooling |
| Exact IP access controls | Yes | Usually separate |
| External analytics account required | No | Usually yes |
| Laravel-native Livewire dashboard | Yes (optional) | Depends on provider |
| SQLite / MySQL / PostgreSQL CI | Yes | Not applicable |

## Quick Start

1. Install the package and publish config plus migrations (see [Installation](#installation)).
2. Run migrations.
3. Enable features explicitly in `config/analytics.php`:

```php
'enabled' => true,

'tracking' => [
    'traffic' => true,
    'errors' => true,
],

'ip_banning' => [
    'enabled' => false,
],

'dashboard' => [
    'enabled' => true,
    'authorization' => 'viewAnalyticsDashboard',
    'middleware' => ['web', 'auth'],
],
```

4. Define dashboard authorization in your application:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewAnalyticsDashboard', fn ($user) => /* your policy */);
```

5. Visit `/analytics` (or your configured `dashboard.path`) when the dashboard is enabled.

When `enabled` is true and tracking toggles are on, middleware is registered on the `web` group automatically.

## Installation

```bash
composer require simba-jirira-source/laravel-analytics
```

The service provider registers automatically via Laravel package discovery.

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

```bash
php artisan vendor:publish --tag=analytics
php artisan migrate
```

| Tag | Contents |
|-----|----------|
| `analytics-config` | `config/analytics.php` |
| `analytics-migrations` | Database migrations |
| `analytics-views` | Blade / Livewire views |
| `analytics-lang` | Translation files |
| `analytics-assets` | Public assets |

See [docs/INSTALLATION.md](docs/INSTALLATION.md) for the full walkthrough.

## Privacy by Default

By default the package:

- does not enable tracking, banning, or the dashboard;
- does not store raw IP addresses;
- hashes visitor identifiers using application-specific salt;
- does not associate authenticated users unless configured;
- excludes dashboard routes from self-tracking.

See [docs/PRIVACY.md](docs/PRIVACY.md). This package provides technical privacy controls but does not itself make an application compliant with any specific privacy law or regulatory framework.

## Compatibility

| Requirement | Supported |
|-------------|-----------|
| PHP | 8.3+ (8.3–8.5 in CI) |
| Laravel | 12, 13 |
| Livewire | 4 (required dependency; dashboard is optional via config) |
| Databases | SQLite, MySQL, PostgreSQL (integration CI) |

## Dashboard

The optional Livewire 4 dashboard provides overview metrics, traffic trends, top pages and referrers, error details, and IP ban management when explicitly enabled and authorized.

See [docs/DASHBOARD.md](docs/DASHBOARD.md).

## Artisan Commands

| Command | Description |
|---------|-------------|
| `analytics:prune` | Remove records older than the configured retention window |
| `analytics:ip-ban {ip}` | Ban an exact IPv4 or IPv6 address |
| `analytics:ip-unban {ip}` | Remove an active ban |

Pruning is not scheduled automatically. See [docs/RETENTION.md](docs/RETENTION.md).

## Configuration

All settings live in `config/analytics.php`. See [docs/CONFIGURATION.md](docs/CONFIGURATION.md) for every key.

Extension points (contracts):

- `SimbaJirira\LaravelAnalytics\Contracts\VisitorIdentifier`
- `SimbaJirira\LaravelAnalytics\Contracts\AnalyticsRecorder`
- `SimbaJirira\LaravelAnalytics\Contracts\ErrorRecorder`

Public integration uses Laravel contracts, config keys, middleware aliases, and Artisan commands. There is no facade; a first-party event API is planned for a future release.

## Testing

```bash
composer install
composer verify
```

Individual gates:

```bash
composer test              # prepare, PHPStan, Pint, type coverage, Pest
composer test:types        # Pest type coverage (skipped automatically on Windows)
composer analyse           # PHPStan (level 7)
composer lint:check        # Pint
composer test:unit         # Pest (sequential on Windows, parallel elsewhere)
composer test:database     # cross-database integration tests
composer security:audit    # Composer security audit
```

PHPStan level 7 via Larastan. Run `composer run prepare` before `composer analyse` when analysing locally.

## Documentation

| Topic | Document |
|-------|----------|
| Installation | [docs/INSTALLATION.md](docs/INSTALLATION.md) |
| Configuration | [docs/CONFIGURATION.md](docs/CONFIGURATION.md) |
| Privacy | [docs/PRIVACY.md](docs/PRIVACY.md) |
| Architecture | [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) |
| Visitor identification | [docs/VISITOR_IDENTIFICATION.md](docs/VISITOR_IDENTIFICATION.md) |
| Dashboard | [docs/DASHBOARD.md](docs/DASHBOARD.md) |
| Retention | [docs/RETENTION.md](docs/RETENTION.md) |
| Releases | [docs/RELEASES.md](docs/RELEASES.md) |
| GitHub repository setup | [docs/GITHUB_REPOSITORY_SETUP.md](docs/GITHUB_REPOSITORY_SETUP.md) |
| Screenshot guidance | [docs/SCREENSHOTS.md](docs/SCREENSHOTS.md) |
| Contributing | [.github/CONTRIBUTING.md](.github/CONTRIBUTING.md) |
| Security policy | [.github/SECURITY.md](.github/SECURITY.md) |
| Changelog | [CHANGELOG.md](CHANGELOG.md) |

## Contributing

Please read [CONTRIBUTING.md](.github/CONTRIBUTING.md) before opening a pull request. See [CODE_OF_CONDUCT.md](CODE_OF_CONDUCT.md).

## Security

Report security issues privately. See [SECURITY.md](.github/SECURITY.md).

## Versioning

This project follows [Semantic Versioning](https://semver.org/). APIs may change before `1.0.0`. See [CHANGELOG.md](CHANGELOG.md).

## License

The MIT License. See [LICENSE.md](LICENSE.md).

## Credits

- [simba-jirira-source](https://github.com/simba-jirira-source)
