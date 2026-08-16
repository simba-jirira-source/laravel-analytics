# v0.6.0 Readiness Report

> **Package:** `simba-jirira-source/laravel-analytics`  
> **Report date:** 2026-08-16  
> **Scope:** v0.6.0 — Foundation cleanup, portability, and performance  
> **Maintainer action required:** This report does **not** tag `v0.6.0`, publish to Packagist, or create a GitHub Release.

---

## Executive summary

v0.6.0 completes pre-1.0 structural cleanup: the public PHP namespace is finalized, the empty facade is removed, release automation is repaired, cross-database CI is in place, retention and dashboard queries are hardened for larger datasets, and `composer security:audit` is part of quality gates. **All local quality commands pass.** The package is ready for maintainer-led `v0.6.0` tagging after green CI on `main`.

---

## Namespace decision

| Item | Decision |
|------|----------|
| Previous namespace | `LaravelAnalytics\LaravelAnalytics\` |
| Final pre-1.0 namespace | `SimbaJirira\LaravelAnalytics\` |
| Service provider | `SimbaJirira\LaravelAnalytics\AnalyticsServiceProvider` |
| Test namespace | `SimbaJirira\LaravelAnalytics\Tests\` |
| Factory namespace | `SimbaJirira\LaravelAnalytics\Database\Factories\` |

**Rationale:** Aligns the vendor namespace with the `simba-jirira-source` Packagist identity before stable `1.0.0`. This is the last planned namespace change.

---

## Breaking changes (pre-1.0)

| Change | Migration |
|--------|-----------|
| Namespace `LaravelAnalytics\LaravelAnalytics\` → `SimbaJirira\LaravelAnalytics\` | Update all `use` statements, config class references, and custom bindings |
| `LaravelAnalyticsServiceProvider` → `AnalyticsServiceProvider` | Package discovery updates automatically via Composer; remove manual provider references to the old class |
| Removed `LaravelAnalytics` facade and empty root class | Use contracts (`AnalyticsRecorder`, `ErrorRecorder`, `VisitorIdentifier`), config keys, middleware aliases, and Artisan commands |
| Re-added wired `dashboard.cache_ttl` (default `0`) | Previously removed as unwired; now functional when set to a positive integer |
| Added `retention.chunk_size` (default `1000`) | Additive; tune for large-table pruning if needed |
| Composite index on `analytics_page_views (viewed_at, visitor_hash)` | Fresh installs only via published migrations; existing adopters may add the index manually |

---

## Facade / public API decision

The empty `LaravelAnalytics` facade and its backing class were **removed**. They provided no stable, intentional API surface.

**Current public integration points:**

- Contracts: `VisitorIdentifier`, `AnalyticsRecorder`, `ErrorRecorder`
- Config keys in `config/analytics.php`
- Middleware aliases: `analytics.track-traffic`, `analytics.record-errors`, `analytics.enforce-ip-ban`, `analytics.dashboard`
- Artisan commands: `analytics:prune`, `analytics:ip-ban`, `analytics:ip-unban`
- Publish tags: `analytics-*`

A first-party event API (e.g. `Analytics::event()`) is deferred to **v0.7.0** per roadmap.

---

## Release workflow changes

| Change | Detail |
|--------|--------|
| Removed `update-changelog.yml` | Prevented duplicate CHANGELOG sections after each GitHub Release |
| Release quality gates | Tag workflow runs `composer validate --strict`, `composer audit`, PHPStan, Pint, type coverage, and Pest before publishing |
| Tag-on-main verification | Release job fails if the tagged commit is not reachable from `origin/main` |
| GitHub Release body | Extracted from the matching `CHANGELOG.md` section; `generate_release_notes: false` |
| Credentials | No secrets stored in repository files; release uses default `GITHUB_TOKEN` permissions |

**Maintainer release process:**

1. Merge to `main` with green CI.
2. Promote `[Unreleased]` in `CHANGELOG.md` to `[0.6.0]` with date.
3. Tag `v0.6.0` on a `main` commit and push.
4. Release workflow creates the GitHub Release from CHANGELOG content.

---

## Database compatibility results

| Driver | CI workflow | Local run (2026-08-16) |
|--------|-------------|------------------------|
| SQLite | `database.yml` matrix | **Pass** — 7 integration tests |
| MySQL 8.4 | `database.yml` matrix | Not run locally (requires service container) |
| PostgreSQL 16 | `database.yml` matrix | Not run locally (requires service container) |

**Integration coverage (`tests/DatabaseIntegration/CrossDatabaseIntegrationTest.php`):**

- Migrations for all analytics tables
- Visitor hash uniqueness constraint
- Page-view persistence
- Error fingerprint aggregation
- Dashboard date metrics (including distinct visitor-days)
- IP ban create/active lookup
- Chunked retention pruning

---

## Performance changes

| Area | Change | Default |
|------|--------|---------|
| Retention pruning | Bounded batch deletes via `retention.chunk_size` | `1000` rows per batch |
| Dashboard overview | Optional query caching via `dashboard.cache_ttl` | `0` (disabled) |
| Dashboard SQL | Driver-aware date expressions via `DatabaseSqlHelper` | SQLite / MySQL / PostgreSQL |
| Page views index | Composite `(viewed_at, visitor_hash)` | Migration |

**Not changed in v0.6.0:** synchronous middleware tracking path, event ingestion (v0.7.0), export streaming (v0.8.0).

---

## Tests added

| File | Purpose |
|------|---------|
| `tests/DatabaseIntegrationTestCase.php` | Driver-aware Testbench database setup |
| `tests/DatabaseIntegration/CrossDatabaseIntegrationTest.php` | Cross-database behavioral coverage |
| `tests/Database/AnalyticsPrunerChunkTest.php` | Chunked page-view pruning |
| `tests/Database/AnalyticsDashboardQueryCacheTest.php` | Overview metrics caching |
| `tests/Feature/ServiceProviderTest.php` | Facade alias removal assertion |

**Test count:** 157 Pest tests (was 150).

---

## Commands run

| Command | Result |
|---------|--------|
| `composer validate --strict` | **Pass** |
| `composer security:audit` | **Pass** (no vulnerability advisories) |
| `composer analyse` | **Pass** (PHPStan level 7, 0 errors) |
| `composer lint:check` | **Pass** |
| `vendor/bin/pest` | **Pass** (157 tests, 351 assertions) |
| `vendor/bin/pest tests/DatabaseIntegration` | **Pass** (7 tests, SQLite) |
| `composer test:types` | **Skipped locally** — Windows type-coverage plugin limitation; CI runs on Ubuntu |

---

## CI workflows

| Workflow | Purpose |
|----------|---------|
| `tests.yml` | PHP 8.3–8.5 × Laravel 12/13 × prefer-lowest/stable × Ubuntu/Windows |
| `database.yml` | SQLite, MySQL, PostgreSQL integration tests (PHP 8.4, Laravel 13) |
| `security.yml` | `composer validate --strict` + `composer audit` (weekly schedule) |
| `static-analysis.yml` | PHPStan |
| `code-style.yml` | Pint |
| `release.yml` | Quality gates + CHANGELOG-driven GitHub Release on tag |

---

## Remaining risks

| Risk | Severity | Notes |
|------|----------|-------|
| MySQL/PostgreSQL not validated locally | Medium | Covered by `database.yml` on GitHub Actions; verify green matrix before tagging |
| Parallel Pest on Windows | Low | File-lock errors on bootstrap cache; CI uses Ubuntu for parallel runs |
| No public event API yet | Info | Planned for v0.7.0; facade removal is intentional |
| Livewire hard dependency | Low | Unchanged; optional dashboard still requires package install of Livewire |
| Abandoned transitive package | Info | `shipfastlabs/agent-detector` reported by audit; `--abandoned=ignore` in CI |
| Existing adopters on old namespace | Medium | Must update imports/bindings when upgrading from `0.5.x` |

---

## Readiness for v0.7.0

**Recommendation: Proceed after v0.6.0 is tagged and CI is green.**

v0.6.0 closes the foundation workstream:

- Namespace is final for 1.0
- Facade slot is clear for a deliberate `Analytics` API in v0.7.0
- Database portability baseline exists for custom events JSON storage
- Release automation will not duplicate CHANGELOG entries

**v0.7.0 should not begin until:**

1. `v0.6.0` is tagged from `main`.
2. `database.yml` matrix is green on GitHub Actions.
3. Packagist reflects `0.6.0` (maintainer publish).

---

## Ready for v0.6.0?

**Recommendation: Yes — pending maintainer release decision.**

Do not tag or publish automatically. Await explicit maintainer approval.

---

*Generated during v0.6.0 implementation. See [POST_RELEASE_IMPROVEMENT_PLAN.md](cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md) for the full roadmap.*
