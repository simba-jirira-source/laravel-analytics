# Release Notes

## [Unreleased]

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

### Changed

- Added `livewire/livewire`, `illuminate/auth`, `illuminate/view`, and `illuminate/validation` runtime dependencies for the dashboard.
- `composer test` now runs `@prepare` before static analysis so Larastan resolves package views.
- PHPStan bootstrap registers the `analytics` view namespace when Larastan does not load package views.
- `RequestExclusionChecker` supports error tracking enablement, IP ban bypass rules, analytics route exclusions, and safe recorder failure handling.
- `PageViewRecorder` delegates visitor persistence to `VisitorService`.
- `DefaultVisitorIdentifier` uses salt + normalized IP + optional UA/auth components.
- Service provider registers traffic, error, and IP-ban middleware when respective features are enabled.
- Added `illuminate/database`, `illuminate/http`, and `illuminate/routing` runtime dependencies.
- Replaced skeleton placeholder migration with domain schema; normalized config and publish tags.

No tagged releases exist yet.
