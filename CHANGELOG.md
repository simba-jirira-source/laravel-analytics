# Release Notes

## [Unreleased](https://github.com/simba-jirira-source/laravel-analytics/compare/v0.1.0...1.x)

### Added

- Optional Livewire 4 analytics dashboard with Tailwind-compatible Blade views, date filters, pagination, error details, and IP ban management.
- `AnalyticsDashboardQuery`, `DashboardAuthorizer`, `AuthorizeAnalyticsDashboard` middleware, and `routes/dashboard.php` (opt-in via config).
- Livewire components under `analytics.*` namespace; gate or invokable authorization support.
- Dashboard Pest/Livewire tests (`tests/Dashboard/*`, `DashboardAuthorizerTest`, `AnalyticsDashboardQueryTest`).
- Configurable analytics retention pruning via `AnalyticsPruner` and `analytics:prune`.
- `docs/RETENTION.md` with retention settings and host-application scheduling guidance.
- Optional IP banning via `EnforceIpBanMiddleware`, `IpBanService`, and `IpUnbanService`.
- Exact IPv4/IPv6 validation, expiry support, and CLI recovery commands (`analytics:ip-ban`, `analytics:ip-unban`).
- Safe HTTP error analytics via `RecordErrorsMiddleware` and `AnalyticsErrorRecorder`.
- Error fingerprinting, safe metadata extraction, and replaceable `analytics.error_recorder` binding.
- Visitor analytics services (`VisitorService`, `VisitorAnalytics`) and privacy-aware default visitor identification.
- IP normalization, configurable hash salt, replaceable `analytics.visitor_identifier`, and `docs/VISITOR_IDENTIFICATION.md`.
- Traffic tracking middleware (`analytics.track-traffic`) and `PageViewRecorder` service.
- Request exclusion checker, analytics recorder contracts, and domain persistence layer.
- Package foundation: Composer metadata, `composer verify`, and normalized `analytics-*` publish tags.

### Changed

- Added `livewire/livewire`, `illuminate/auth`, `illuminate/view`, and `illuminate/validation` runtime dependencies for the dashboard.
- `composer test` now runs `@prepare` before static analysis so Larastan resolves package views.
- `RequestExclusionChecker` supports error tracking enablement, IP ban bypass rules, analytics route exclusions, and precise package-recorder failure detection.
- `PageViewRecorder` delegates visitor persistence to `VisitorService`.
- `DefaultVisitorIdentifier` uses salt + normalized IP + optional UA/auth components.
- Service provider registers traffic middleware when tracking is enabled.
- Added `illuminate/database`, `illuminate/http`, and `illuminate/routing` runtime dependencies.
- Replaced skeleton placeholder migration with domain schema; normalized config and publish tags.

## [v0.1.0](https://github.com/simba-jirira-source/laravel-analytics/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
