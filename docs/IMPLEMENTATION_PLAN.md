# Laravel Analytics — Implementation Plan

> **Status:** Phase 2 complete (configuration and persistence). No request tracking implemented.
>
> **Last updated:** 2026-08-16
>
> **Package:** `simba-jirira-source/laravel-analytics` · **Namespace:** `LaravelAnalytics\LaravelAnalytics`

---

## Phase 2 Report

### What was implemented

Phase 2 added the full configuration schema and persistence layer without request tracking or dashboard functionality:

- **Configuration** — expanded `config/analytics.php` with privacy-conscious defaults for dashboard, tracking, IP banning, privacy, ignored paths, retention, and optional user association.
- **Runtime dependency** — added `illuminate/database` for Eloquent models and migrations.
- **Migrations** — four domain tables replacing the skeleton placeholder migration.
- **Models** — `Visitor`, `PageView`, `AnalyticsError`, `IpBan` with typed casts and relationships.
- **Factories** — test factories for all models, including inactive/expired states for `IpBan`.
- **Tests** — config default tests, migration tests, model persistence/cast/relationship tests via `DatabaseTestCase`.

**Not implemented (by design):** middleware, tracking services, error recording, IP ban enforcement, dashboard, Artisan domain commands (`analytics:install`, `analytics:prune`, etc.).

---

### Migrations / models / configuration added

**Configuration (`config/analytics.php`):**

| Section | Key defaults |
|---------|----------------|
| Master switch | `enabled => false` |
| Dashboard | `enabled => false`, path `analytics`, middleware `['web', 'auth']`, authorization `null` |
| Tracking | `traffic => false`, `errors => false` |
| IP banning | `enabled => false`, `blocked_status => 403` |
| Privacy | `store_raw_ip => false`, `hash_ips => true`, `track_authenticated_users => false` |
| Ignored | dashboard paths/routes excluded by default |
| Retention | `days => 90`, all prune flags `true` |
| User | `model => null` (no host User coupling) |

**Migrations:**

| File | Table |
|------|-------|
| `2026_01_01_000001_create_analytics_visitors_table.php` | `analytics_visitors` |
| `2026_01_01_000002_create_analytics_page_views_table.php` | `analytics_page_views` |
| `2026_01_01_000003_create_analytics_errors_table.php` | `analytics_errors` |
| `2026_01_01_000004_create_analytics_ip_bans_table.php` | `analytics_ip_bans` |

**Deleted:** `2026_01_01_000000_create_laravel_analytics_placeholder_table.php`

**Models (`src/Models/`):**

| Model | Table | Notes |
|-------|-------|-------|
| `Visitor` | `analytics_visitors` | HasMany page views; datetime casts |
| `PageView` | `analytics_page_views` | BelongsTo visitor; immutable (`$timestamps = false`) |
| `AnalyticsError` | `analytics_errors` | Fingerprint + occurrence metadata |
| `IpBan` | `analytics_ip_bans` | Active/expiry casts |

**Factories (`database/factories/`):** `VisitorFactory`, `PageViewFactory`, `AnalyticsErrorFactory`, `IpBanFactory`

---

### Database decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Visits/sessions table | Not added | Not required for current domain model; derivable from page views in Phase 3/4 |
| User foreign keys | Nullable `user_id` columns, no FK constraint | Avoid coupling to host `users` table |
| Raw IP storage | Column present, default config omits population | Supports opt-in `store_raw_ip` in Phase 4 |
| Page view immutability | No `updated_at` on `analytics_page_views` | Append-only event log |
| Visitor ↔ page view | Optional `visitor_id` FK with `nullOnDelete` | Supports denormalized `visitor_hash` queries |
| IP address column width | `varchar(45)` | IPv4 and IPv6 |
| Hash columns | `varchar(64)` | SHA-256 hex |
| Indexes | Per-table indexes on query columns (dates, hashes, paths, fingerprints, IP+active) | Supports dashboard and retention queries |
| Test database | SQLite in-memory via Testbench `testbench` connection | Fast, portable package tests |

---

### Tests added

| File | Coverage |
|------|----------|
| `tests/Unit/ConfigTest.php` | Disabled defaults, privacy settings, ignored paths, retention, user model null, config override |
| `tests/Database/MigrationTest.php` | All four tables created; placeholder removed; visitor FK |
| `tests/Database/ModelTest.php` | Factory persistence, casts, relationships, IP ban factory states |
| `tests/DatabaseTestCase.php` | SQLite in-memory + migration loading |
| `tests/Pest.php` | Separate `DatabaseTestCase` binding for `tests/Database/` |
| `phpunit.xml.dist` | Added `Database` testsuite |

**Suite totals after Phase 2:** 29 tests, 64 assertions.

---

### Commands run

```powershell
composer update illuminate/database --no-interaction
composer dump-autoload
composer lint
composer verify
```

---

### Results

| Gate | Result |
|------|--------|
| `composer validate --strict` | Passed |
| PHPStan (level 7) | Passed |
| Pint | Passed |
| Pest type coverage | 100% |
| Pest test suite | **29 passed** (5975 ms) |

---

### Remaining risks

| Item | Status |
|------|--------|
| No request tracking yet | Expected — Phase 3 |
| Config keys unused until later phases | Expected — services read them in Phases 3–8 |
| `hash_salt` resolution logic not implemented | Phase 4 visitor identification |
| Dashboard authorization mechanism unset (`null`) | Phase 8 |
| Multi-database / non-relational support | Out of scope; SQLite + common SQL targets only |
| Published migration upgrades for early adopters | Document in Phase 9 if schema changes |

**No blockers prevent Phase 3.**

---

### Phase 3 readiness

| Prerequisite | Status |
|--------------|--------|
| Domain schema and models | Ready |
| Privacy-conscious config defaults | Ready |
| `analytics.enabled` master switch | Ready |
| Ignored paths include dashboard routes | Ready |
| Page view / visitor tables | Ready for middleware persistence |
| Factories for test data | Ready |
| Quality gates green | Ready |

**Phase 3 scope:** traffic tracking middleware and services — record page views, respect exclusions, capture status/duration, no dashboard self-tracking. No visitor hashing logic beyond schema (Phase 4).

---

## Phase 1 Report (archived summary)

Normalized package skeleton: Composer metadata, `composer verify`, `analytics-*` publish tags, `config/analytics.php` master switch, baseline provider tests. See git history for full file list.

---

## Current state (summary)

Configuration and persistence layer complete. Four analytics tables with Eloquent models and factories. All features disabled by default in config. No HTTP tracking, error recording, IP ban middleware, or dashboard.

---

## Target architecture (summary)

Self-hosted first-party analytics for Laravel 13+ with optional Livewire 4 dashboard. Layers: middleware → services/contracts → Eloquent → optional UI.

---

## Dependency decisions

| Decision | Choice |
|----------|--------|
| Foundation | Official Laravel package skeleton |
| Runtime | `illuminate/database` ^12\|\|^13, `illuminate/support` ^12\|\|^13 |
| PHP | `^8.3` |
| Testbench | ^10 (L12) / ^11 (L13) |
| Pest | ^4.6\|\|^5.0 |
| Static analysis | Larastan ^3.9, level 7 |
| Livewire | ^4 — Phase 8 |

---

## Implementation phases

| Phase | Scope | Status |
|-------|-------|--------|
| **0** | Discovery | Complete |
| **1** | Package foundation | Complete |
| **2** | Config + database | **Complete** |
| **3** | Traffic analytics | Next |
| **4** | Visitor analytics | Pending |
| **5** | Error analytics | Pending |
| **6** | IP banning | Pending |
| **7** | Retention | Pending |
| **8** | Livewire dashboard | Pending |
| **9** | OSS documentation | Pending |
| **10** | CI automation | Pending |
| **11** | Packagist readiness | Pending |
| **12** | v1 hardening | Pending |

---

## Unresolved decisions

1. **Namespace rename** — defer until pre-1.0.
2. **PHP minimum 8.4** — defer until CI drops 8.3.
3. **Pest 5 pin** — Phase 10.
4. **Livewire dependency class** — Phase 8.
5. **Visitor identification algorithm** — Phase 4.
6. **Queue-backed recording** — optional future enhancement.

---

## Next step

**Phase 3 — Traffic analytics** — await explicit go-ahead. Do not implement until requested.
