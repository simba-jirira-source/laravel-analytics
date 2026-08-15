# Release Notes

## [Unreleased](https://github.com/simba-jirira-source/laravel-analytics/compare/v0.1.0...1.x)

### Added

- Traffic tracking middleware (`analytics.track-traffic`) and `PageViewRecorder` service.
- Request exclusion checker, default visitor identifier, and analytics recorder contracts.
- Full `config/analytics.php` with privacy-conscious defaults for tracking, dashboard, IP banning, and retention.
- Domain migrations and Eloquent models: `analytics_visitors`, `analytics_page_views`, `analytics_errors`, `analytics_ip_bans`.
- Model factories and database test coverage.
- Package foundation: Composer metadata, `composer verify`, and normalized `analytics-*` publish tags.

### Changed

- Service provider registers traffic middleware when tracking is enabled.
- Added `illuminate/database`, `illuminate/http`, and `illuminate/routing` runtime dependencies.
- Replaced skeleton placeholder migration with domain schema.
- Renamed configuration from `laravel-analytics.php` to `analytics.php`.
- Renamed routes file to `routes/web.php` and normalized view/translation namespaces to `analytics`.
- Replaced skeleton placeholder command with `analytics:placeholder`.

## [v0.1.0](https://github.com/simba-jirira-source/laravel-analytics/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
