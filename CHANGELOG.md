# Release Notes

## [Unreleased]

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

[0.5.0]: https://github.com/simba-jirira-source/laravel-analytics/releases/tag/v0.5.0
