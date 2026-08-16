# Release Notes

## [Unreleased](https://github.com/simba-jirira-source/laravel-analytics/compare/v0.1.0...1.x)

### Added

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

- `RequestExclusionChecker` supports error tracking enablement, IP ban bypass rules, analytics route exclusions, and precise package-recorder failure detection.
- `PageViewRecorder` delegates visitor persistence to `VisitorService`.
- `DefaultVisitorIdentifier` uses salt + normalized IP + optional UA/auth components.
- Service provider registers traffic middleware when tracking is enabled.
- Added `illuminate/database`, `illuminate/http`, and `illuminate/routing` runtime dependencies.
- Replaced skeleton placeholder migration with domain schema; normalized config and publish tags.

## [v0.1.0](https://github.com/simba-jirira-source/laravel-analytics/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
