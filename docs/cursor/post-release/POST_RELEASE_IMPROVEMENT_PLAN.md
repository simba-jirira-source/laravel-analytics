# Laravel Analytics — Post-Release Improvement Plan

## Project

**Package:** `simba-jirira-source/laravel-analytics`  
**Current public release at plan creation:** `v0.5.0`  
**Target:** production-quality `v1.0.0`

This plan begins after the first public GitHub and Packagist release. It is intended to improve an existing working package, not rebuild it.

## Existing baseline

The package already includes Laravel 12/13 support, PHP 8.3+, Livewire 4 dashboard, traffic/page-view analytics, visitor tracking, HTTP error analytics, exact IPv4/IPv6 banning, retention pruning, Pest, Larastan/PHPStan, Pint, GitHub Actions, Packagist distribution, MIT licensing, and OSS documentation.

## Global rules

1. Inspect the current repository before modifying it.
2. Verify every finding in this plan against current `main`; the repository may have changed.
3. Preserve working behaviour except for explicitly documented pre-1.0 cleanup.
4. Implement only one roadmap version per Cursor request.
5. Every bug fix requires a regression test.
6. Every public feature requires behavioural tests.
7. Run Composer validation, Pest, Pint and static analysis for every version.
8. Run database-specific tests when SQL or schema behaviour changes.
9. Never claim compatibility not covered by CI.
10. Keep privacy-sensitive features opt-in.
11. Never collect arbitrary headers, request bodies, cookies, session payloads, credentials, secrets or tokens.
12. Never fabricate releases, stars, contributors, download counts or Packagist statistics.
13. Do not automatically tag or publish a version.
14. Treat public contracts, config keys, facade methods, commands, routes, events, middleware aliases and schemas as compatibility-sensitive.

---

# Immediate post-release findings to verify

## Public release metadata

Confirm README and Packagist-facing text reflects the actual latest release. Remove stale statements such as “no release has been tagged” once releases exist. Add real Packagist/latest-version badges only when they resolve correctly.

## Namespace

If the public namespace is still:

```php
SimbaJirira\LaravelAnalytics\
```

review migrating before 1.0 to:

```php
SimbaJirira\LaravelAnalytics\
```

This is a breaking pre-1.0 change and should not be postponed beyond `v0.6.0` if it is going to happen.

## Facade

If the package still exposes an empty `LaravelAnalytics` facade/service class, either remove it or turn it into a small intentional stable API. Do not expose internal services merely to justify having a facade.

## Database portability

Add focused integration coverage for SQLite, MySQL and PostgreSQL. The full PHP/Laravel matrix does not need to run against every DB; use a focused database matrix.

## Release workflow

Verify release notes are meaningful, CHANGELOG entries are not duplicated by automation, stable releases are built from approved `main` commits, and release quality gates are at least as strict as normal CI.

## Performance

Review pruning, dashboard aggregate queries, high-cardinality indexes, synchronous tracking cost, caching, concurrency and large data sets.

## Product direction

The most valuable product addition is first-party custom events, which can become the basis for goals and conversions:

```php
Analytics::event('registered');

Analytics::event('subscription_started', [
    'plan' => 'business',
]);
```

---

# v0.6.0 — Foundation Cleanup, Portability and Performance

## Objective

Make breaking structural corrections while the package is still pre-1.0, strengthen database portability and CI, fix release automation, and establish a production-performance baseline.

## Workstreams

### Namespace cleanup

Audit the current namespace. If still justified, migrate `SimbaJirira\LaravelAnalytics\` to `SimbaJirira\LaravelAnalytics\`. Update Composer PSR-4, provider discovery, facade, contracts, models, services, middleware, Livewire components, workbench, tests, PHPStan configuration, README and documentation. Document this as a pre-1.0 breaking change.

### Public facade/API cleanup

Remove any empty facade or give it a minimal intentional purpose. Do not publish a broad convenience API that duplicates internal services.

### Release workflow repair

Ensure one CHANGELOG section per version, meaningful GitHub Release notes, quality gates before release, safe repository permissions, and no workflow that rewrites release history incorrectly.

### README / Packagist accuracy

Update release status, installation command, supported matrix, badges, repository description, homepage and topics.

### Database CI

Add focused tests for SQLite, MySQL and PostgreSQL covering migrations, visitor uniqueness, page-view writes, error aggregation, date metrics, IP bans and pruning.

### Performance hardening

Measure before changing. Consider chunked pruning, bounded deletes, dashboard aggregate caching, composite indexes, query-count tests and avoiding duplicate metadata parsing.

### Dependency security

Add `composer audit` to CI or a dedicated security workflow.

## Acceptance criteria

- namespace decision is final and documented
- no purposeless public facade remains
- public release metadata is accurate
- release workflow does not duplicate CHANGELOG content
- SQLite/MySQL/PostgreSQL integration tests pass
- performance baseline is documented
- pruning is safe for larger datasets
- `composer audit` is part of quality gates
- Pest, Pint, PHPStan/Larastan and Composer validation pass

---

# v0.7.0 — Custom Events, Goals and Conversions

## Objective

Turn the package from passive traffic analytics into first-party application analytics.

## Custom event API

Provide one clear API, such as:

```php
Analytics::event('registered');
Analytics::event('checkout_completed', ['plan' => 'pro']);
```

If the facade is removed, expose an equally clean injectable service API.

## Event persistence

Add a portable `analytics_events` model/table with a validated event name, timestamp, optional visitor/user association, optional route/path context and bounded JSON properties.

Define limits for event-name length, property count, key length, value length, nesting depth and total serialized size. Do not capture arbitrary request data.

## Goals and conversions

Allow applications to define goals based on event names. Support conversion count, unique converting visitors, date filtering and conversion rate only when the denominator is well-defined.

## Dashboard

Add event count, trends, top events, event detail, goals and conversions.

## Tests

Cover validation, persistence, property limits, privacy boundaries, disabled analytics behaviour, visitor/user association, conversion calculations, dashboard authorization and cross-database JSON behaviour.

---

# v0.8.0 — Analytics Depth and Export

## Objective

Add deeper reporting while preserving privacy-conscious defaults.

## Optional browser/device metadata

Derive browser, OS and device category from already-enabled user-agent collection. Avoid invasive fingerprinting. Make this independently configurable.

## Bot filtering

Add deterministic configurable bot filtering with documented limitations and an override point where appropriate.

## UTM campaign tracking

Allow only `utm_source`, `utm_medium`, `utm_campaign`, `utm_term` and `utm_content`. Never persist arbitrary query parameters. Preserve stripping of unrelated query strings.

## Period comparisons

Support current versus previous comparable period, including safe handling for zero/missing previous values.

## Export

Add authorized CSV and JSON export with date filters and chunked/streamed output. Never reveal internal sensitive fields or build large exports fully in memory.

---

# v0.9.0 — Release Candidate and Operational Hardening

## Objective

Stop substantial feature expansion and prepare for stable 1.0 APIs.

## Fresh installation

Verify clean Laravel installs for all declared support combinations that are practical, including headless and dashboard-enabled configurations.

## Upgrade UX

Create `docs/UPGRADING.md` covering migrations/config changes from earlier `0.x` versions.

## Livewire dependency decision

Decide whether Livewire remains a hard dependency, becomes optional, or the dashboard moves to a companion package. Base the decision on real user/maintenance trade-offs, not aesthetics.

## Benchmarks

Build repeatable fixtures for 100k page views, 1m page views where practical, 100k events, pruning, 7-day/30-day dashboard queries, top pages/events and conversion queries. Keep expensive benchmarks out of standard CI if necessary.

## Safe diagnostics

Consider `php artisan analytics:status` showing package version, DB driver, enabled features, retention and migration readiness without exposing keys/salts/credentials.

## OSS adoption

Improve screenshots, examples, troubleshooting, architecture diagram, first-contribution guidance and good-first-issue candidates.

## API freeze candidate

Create `docs/PUBLIC_API.md` documenting what is intended to be stable in 1.0.

---

# v1.0.0 — Stable API and Production Release

## Objective

Declare the first stable public API. Do not treat 1.0 as a feature-release milestone.

## Public API freeze

Finalize Composer name, PHP namespace, provider, facade/API, contracts, config keys, middleware aliases, Artisan commands, publish tags, public events, routes and documented extension points. Remove accidental APIs before stable release.

## Compatibility matrix

Document only combinations actually tested in CI across PHP, Laravel, SQLite/MySQL/PostgreSQL and Livewire where applicable.

## Security review

Re-test dashboard/Livewire/export authorization, IP-ban behaviour, proxy assumptions, error redaction, custom-event limits, UTM sanitization, XSS escaping, SQL injection safety, command validation and secret leakage.

## Privacy review

Document exact defaults and optional data collection for IPs, visitor hashes, users, referrers, user agents, events, UTM values, retention, exports and backups. Never claim legal compliance.

## Database/performance review

Audit migrations, indexes, upgrade migrations, pruning performance, event growth, error aggregation, exports, dashboard queries and cross-driver SQL.

## Final documentation

Finalize README, INSTALLATION, CONFIGURATION, PUBLIC_API, UPGRADING, PRIVACY, SECURITY, DASHBOARD, CUSTOM_EVENTS, EXPORTS, TROUBLESHOOTING and CHANGELOG.

## Final gates

Run `composer validate --strict`, `composer audit`, `composer verify`, full compatibility/database CI equivalents and benchmark smoke checks.

Create `docs/V1_FINAL_RELEASE_REPORT.md` with supported matrix, stable public API, quality results, DB results, security/privacy review, performance summary, known limitations, blockers and release recommendation.

Do not tag or publish `v1.0.0` automatically. Wait for explicit maintainer approval.

---

# Suggested post-1.0 backlog

Only pursue these when real user demand justifies them: queued ingestion, Redis buffering, custom dimensions, funnels, cohorts, retention reports, scheduled reports, email summaries, dashboard widget API, multi-site analytics, multi-tenancy integrations, user-supplied geolocation resolvers, alternative storage drivers, ClickHouse adapter or public analytics APIs.
