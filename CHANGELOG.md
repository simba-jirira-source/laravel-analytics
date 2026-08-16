# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.6.0] - 2026-08-16

Foundation cleanup before 1.0: namespace migration, database portability CI, performance hardening, release workflow repair, and dependency security auditing.

### Added

- Cross-database integration tests for SQLite, MySQL, and PostgreSQL (`tests/DatabaseIntegration/*`).
- `DatabaseIntegration` PHPUnit testsuite in `phpunit.xml.dist`.
- `DatabaseSqlHelper` for driver-aware dashboard date SQL across SQLite, MySQL, and PostgreSQL.
- Configurable dashboard aggregate caching via `dashboard.cache_ttl` (default `0`, disabled).
- Configurable retention delete batching via `retention.chunk_size` (default `1000`).
- Composite index on `analytics_page_views (viewed_at, visitor_hash)` for dashboard queries.
- `database.yml` GitHub Actions workflow for SQLite, MySQL 8.4, and PostgreSQL 16.
- `security.yml` GitHub Actions workflow for scheduled and on-push Composer security auditing.
- `composer security:audit` script and inclusion in `composer verify`.
- `composer test:database` script for cross-database integration tests.
- Regression tests for chunked pruning and dashboard query caching.
- Windows-compatible Composer wrappers: `tools/type-coverage.php` and `tools/run-pest.php`.
- `docs/V0_6_0_READINESS_REPORT.md`.

### Changed

- **Breaking:** PHP namespace migrated from `LaravelAnalytics\LaravelAnalytics\` to `SimbaJirira\LaravelAnalytics\`.
- **Breaking:** Service provider renamed to `SimbaJirira\LaravelAnalytics\AnalyticsServiceProvider`.
- **Breaking:** Removed empty `LaravelAnalytics` facade and root service class; use contracts, config keys, middleware aliases, and container bindings instead.
- `AnalyticsPruner` deletes stale rows in bounded batches instead of single large deletes.
- `AnalyticsDashboardQuery` uses driver-aware SQL for traffic trends and distinct visitor-day counts.
- Release workflow runs quality gates (including `composer audit`) before publishing, verifies tags are reachable from `main`, and uses matching `CHANGELOG.md` sections for GitHub Release bodies.
- README updated with Packagist badge, current release status, PHP/Laravel support, CI badges, and Windows testing notes.
- `docs/CONFIGURATION.md` documents `dashboard.cache_ttl` and `retention.chunk_size`.
- `composer test:types` skips type coverage on Windows where the Pest plugin is unsupported; CI remains authoritative on Ubuntu.
- `composer test:unit` runs Pest sequentially on Windows to avoid Testbench bootstrap cache locking under parallel workers.

### Removed

- `LaravelAnalytics` facade alias and empty facade/service class.
- `update-changelog.yml` workflow that duplicated `CHANGELOG.md` sections after each GitHub Release.

## [0.5.0] - 2026-08-16

First public pre-release: traffic tracking, visitor analytics, error analytics, IP banning, Livewire dashboard, retention pruning, OSS documentation, CI/CD, and Phase 12 hardening.

### Added

- Open-source documentation: README, installation, configuration, privacy, architecture, dashboard, and release guides.
- Community files: CONTRIBUTING, SECURITY, CODE_OF_CONDUCT, issue forms, and pull request template.
- Optional Livewire 4 analytics dashboard with Tailwind-compatible Blade views, date filters, pagination, error details, and IP ban management.
- `AnalyticsDashboardQuery`, `DashboardAuthorizer`, `AuthorizeAnalyticsDashboard` middleware, and `routes/dashboard.php` (opt-in via config).
- Livewire components under the `analytics.*` namespace; gate or invokable authorization support.
- Dashboard Pest/Livewire tests (`tests/Dashboard/*`, `DashboardAuthorizerTest`, `AnalyticsDashboardQueryTest`).
- Configurable analytics retention pruning via `AnalyticsPruner` and `analytics:prune`.
- `docs/RETENTION.md` with retention settings and host-application scheduling guidance.
- Optional IP banning via `EnforceIpBanMiddleware`, `IpBanService`, and `IpUnbanService`.
- Exact IPv4/IPv6 validation, expiry support, and CLI recovery commands (`analytics:ip-ban`, `analytics:ip-unban`).
- Safe HTTP error analytics via `RecordErrorsMiddleware` and `AnalyticsErrorRecorder`.
- Error fingerprinting, safe metadata extraction, and replaceable `analytics.error_recorder` binding.
- Visitor analytics services (`VisitorService`, `VisitorAnalytics`) and privacy-aware default visitor identification.
- IP normalization, configurable hash salt, replaceable `analytics.visitor_identifier`, and `docs/VISITOR_IDENTIFICATION.md`.
- Traffic tracking middleware and `PageViewRecorder` service.
- Request exclusion checker, analytics recorder contracts, and domain persistence layer.
- Package foundation: Composer metadata, `composer verify`, and normalized `analytics-*` publish tags.
- GitHub Actions workflows for tests, static analysis, code style, and tag-driven releases; Dependabot configuration.
- `SensitiveMessageRedactor` for unified error message redaction.
- `analytics_recorder` config key for replaceable traffic recorder binding.
- `ip_banning.bypass_paths` and `ip_banning.bypass_route_names` for explicit IP-ban bypass configuration.
- `docs/V1_READINESS_REPORT.md` from Phase 12 hardening review.

### Changed

- Added `livewire/livewire`, `illuminate/auth`, `illuminate/view`, and `illuminate/validation` runtime dependencies for the dashboard.
- `composer test` now runs `@prepare` before static analysis so Larastan resolves package views.
- PHPStan bootstrap registers the `analytics` view namespace when Larastan does not load package views.
- `RequestExclusionChecker` supports error tracking enablement, IP ban bypass rules, analytics route exclusions, and safe recorder failure handling.
- `PageViewRecorder` delegates visitor persistence to `VisitorService` and wraps writes in a database transaction.
- `DefaultVisitorIdentifier` uses salt + normalized IP + optional UA/auth components.
- Service provider registers traffic, error, and IP-ban middleware when respective features are enabled.
- Added `illuminate/database`, `illuminate/http`, and `illuminate/routing` runtime dependencies.
- Replaced skeleton placeholder migration with domain schema; normalized config and publish tags.
- All dashboard Livewire components authorize on every request via `bootInteractsWithAnalyticsDashboard()`.
- IP bans no longer bypass via `analytics.ignored.paths`; use `ip_banning.bypass_paths` when recovery access is required.
- Dashboard `visits` metric counts distinct visitor-days (semantically distinct from `unique_visitors`).
- Referrer URLs are stored without query strings.
- `AnalyticsErrorRecorder` uses atomic database increments for occurrence counts.
- Invalid `ip_banning.blocked_status` values fall back to 403.

### Fixed

- Livewire dashboard authorization bypass on child components (`TrafficOverview`, `RecentErrors`, `ErrorDetails`, etc.).
- Race conditions when aggregating errors by fingerprint under concurrent requests.
- Duplicate `visits` and `unique_visitors` dashboard metrics returning identical values.

### Removed

- `analytics:placeholder` scaffold command, placeholder view, and translation file.
- Unwired config keys: `dashboard.cache_ttl`, `trusted_proxies`, and `user.model` / `user.foreign_key`.

[0.6.0]: https://github.com/simba-jirira-source/laravel-analytics/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/simba-jirira-source/laravel-analytics/releases/tag/v0.5.0
