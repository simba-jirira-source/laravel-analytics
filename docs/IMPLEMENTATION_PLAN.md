# Laravel Analytics — Implementation Plan

> **Status:** Phase 1 complete (package foundation). No analytics tracking implemented.
>
> **Last updated:** 2026-08-15
>
> **Package:** `simba-jirira-source/laravel-analytics` · **Namespace:** `LaravelAnalytics\LaravelAnalytics`

---

## Phase 1 Report

### What was implemented

Phase 1 normalized the official package skeleton for Laravel Analytics foundation work:

- **Composer metadata** — added `type: library`, description, keywords, and `support` URLs.
- **`composer verify`** — consolidated gate running `composer validate --strict` plus the full `composer test` pipeline.
- **Configuration** — renamed to `config/analytics.php` with `analytics.enabled` defaulting to `false`.
- **Publish tags** — normalized to `analytics`, `analytics-config`, `analytics-migrations`, `analytics-views`, `analytics-lang`, `analytics-assets`.
- **Routes** — renamed to `routes/web.php` (placeholder route remains commented out).
- **View/translation namespaces** — normalized to `analytics`.
- **Artisan command** — replaced skeleton command with `analytics:placeholder` (`AnalyticsPlaceholderCommand`).
- **Tests** — removed trivial unit test; added meaningful unit and publish-tag feature coverage.
- **Documentation** — updated README (status/requirements, publish tags), CHANGELOG, AGENTS.md, CONTRIBUTING.md.
- **PHPStan** — added `--memory-limit=512M` to `composer analyse` for reliable local/Windows execution.

**Not implemented (by design):** analytics tracking, domain migrations/models, middleware, Livewire, dashboard, privacy docs.

---

### Files created or changed

| Action | Path |
|--------|------|
| Created | `config/analytics.php` |
| Created | `routes/web.php` |
| Created | `src/Console/Commands/AnalyticsPlaceholderCommand.php` |
| Created | `tests/Feature/ServiceProviderTest.php` |
| Created | `tests/Unit/LaravelAnalyticsTest.php` |
| Deleted | `config/laravel-analytics.php` |
| Deleted | `routes/laravel-analytics.php` |
| Deleted | `src/Console/Commands/LaravelAnalyticsCommand.php` |
| Deleted | `tests/Feature/ExampleTest.php` |
| Deleted | `tests/Unit/ExampleTest.php` |
| Modified | `composer.json` |
| Modified | `composer.lock` |
| Modified | `src/LaravelAnalyticsServiceProvider.php` |
| Modified | `lang/en/messages.php` |
| Modified | `resources/views/placeholder.blade.php` |
| Modified | `README.md` |
| Modified | `CHANGELOG.md` |
| Modified | `AGENTS.md` |
| Modified | `.github/CONTRIBUTING.md` |
| Modified | `docs/IMPLEMENTATION_PLAN.md` |

**Unchanged (Phase 2):** `database/migrations/2026_01_01_000000_create_laravel_analytics_placeholder_table.php`, core `LaravelAnalytics` class, facade, workbench files.

---

### Architectural decisions (Phase 1)

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Namespace | Keep `LaravelAnalytics\LaravelAnalytics\` | Avoid large rename before public release; matches skeleton |
| Service provider name | Keep `LaravelAnalyticsServiceProvider` | Consistent with retained namespace |
| Config key / file | `analytics` / `config/analytics.php` | Aligns with master spec |
| Publish tags | `analytics-*` | Aligns with master spec |
| Master switch | `analytics.enabled = false` | Safe default before tracking exists |
| PHP minimum | `^8.3` | CI matrix tests 8.3–8.5; bump to `^8.4` deferred |
| Pest | Keep `^4.6\|\|^5.0` | Local Pest 4.7.8 passes all gates; Pest 5 upgrade deferred to Phase 10 |
| Laravel 12 support | Retained | Dual-major support until v1 policy set |
| Provider rename to `AnalyticsServiceProvider` | Deferred | Would require namespace rename; not needed for foundation |

---

### Tests added

| File | Coverage |
|------|----------|
| `tests/Feature/ServiceProviderTest.php` | Singleton binding, config merge, translations, views, `analytics:placeholder` command, `analytics-config` and `analytics-migrations` publish tags |
| `tests/Unit/LaravelAnalyticsTest.php` | Direct instantiation, disabled-by-default config |
| `tests/ArchTest.php` | Unchanged — strict types, security preset, banned functions |

**Removed:** `tests/Unit/ExampleTest.php` (`expect(true)->toBeTrue()`).

**Suite totals after Phase 1:** 14 tests, 20 assertions (includes arch tests).

---

### Commands run

```powershell
composer update --no-interaction
composer verify
```

`composer verify` executes:

1. `composer validate --strict`
2. `composer test` → `@analyse`, `@lint:check`, `@test:types`, `@test:unit`

---

### Test results

| Gate | Result |
|------|--------|
| `composer validate --strict` | Passed |
| PHPStan (level 7) | Passed (0 errors, 512M memory limit) |
| Pint (`--test`) | Passed |
| Pest type coverage | 100% |
| Pest test suite | **14 passed** (4772 ms) |

---

### Remaining risks or blockers

| Item | Status |
|------|--------|
| Packagist publication | Blocker for public install — maintainer action (Phase 11) |
| Placeholder migration | `laravel_analytics_placeholder` table remains; replaced in Phase 2 |
| Pest 5 baseline | Not upgraded; spec prefers 5+ — address in Phase 10 |
| PHP 8.4 minimum | Spec prefers 8.4+; kept 8.3 for CI matrix compatibility |
| Premature OSS docs | PRIVACY, ARCHITECTURE, CoC, PR template — Phase 9 |
| Livewire | Not installed — Phase 8 |
| Namespace verbosity | Accepted for 0.x; revisit before 1.0 |

**No blockers prevent Phase 2.**

---

### Phase 2 readiness

| Prerequisite | Status |
|--------------|--------|
| Package boots via discovery | Ready |
| `config/analytics.php` exists with safe defaults | Ready — extend with privacy/tracking keys in Phase 2 |
| Publish tags for config/migrations | Ready and tested |
| Quality gates green | Ready |
| Placeholder migration | Present — replace with domain tables in Phase 2 |
| Service provider wiring pattern | Ready for models, factories, extended config |

**Phase 2 scope:** implement full `config/analytics.php`, domain migrations (`analytics_page_views`, `analytics_visitors`, `analytics_errors`, `analytics_ip_bans`), models, casts, indexes, factories, and database tests. No request tracking yet.

---

## Phase 0 Report (archived summary)

Discovery confirmed a configured Laravel official package skeleton. See git history / prior plan version for full Phase 0 audit. Key findings carried forward:

- Repository is a reusable Composer package with Testbench workbench.
- Product analytics not yet implemented.
- Master spec conflicts documented and partially resolved in Phase 1 (config/tags/metadata).

---

## Current state (summary)

Package foundation normalized: Composer metadata complete, `analytics-*` conventions in place, disabled-by-default config, baseline tests prove provider wiring and publishing. Placeholder migration and empty core class remain for Phase 2+.

---

## Target architecture (summary)

Self-hosted first-party analytics for Laravel 13+ with optional Livewire 4 dashboard. Layers: middleware → services/contracts → Eloquent → optional UI. Privacy-conscious defaults, opt-in IP banning, safe error recording, retention pruning, protected dashboard.

```
Request → exclusions → optional ban middleware → app → response → analytics capture → persistence
```

---

## Dependency decisions

| Decision | Choice |
|----------|--------|
| Foundation | Official Laravel package skeleton |
| Runtime Laravel | `illuminate/support` ^12\|\|^13 |
| PHP | `^8.3` (Phase 1; consider `^8.4` when dropping 8.3 from CI) |
| Testbench | ^10 (L12) / ^11 (L13) |
| Pest | ^4.6\|\|^5.0 (Pest 4 verified locally) |
| Static analysis | Larastan ^3.9, level 7 |
| Livewire | ^4 — Phase 8 (`require-dev` when added) |

---

## Implementation phases

| Phase | Scope | Status |
|-------|-------|--------|
| **0** | Discovery | Complete |
| **1** | Package foundation | **Complete** |
| **2** | Config + database | Next |
| **3** | Traffic analytics | Pending |
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

## Unresolved decisions (post Phase 1)

1. **Namespace rename** — defer until pre-1.0 or maintainer request.
2. **PHP minimum 8.4** — defer until CI drops 8.3.
3. **Pest 5 pin** — defer to Phase 10 CI hardening.
4. **Laravel 12 support window** — maintain through 0.x unless decided otherwise.
5. **Livewire dependency class** — `require-dev` + `suggest` vs `require` (Phase 8).
6. **Authenticated user tracking model** — Phase 4.
7. **Visits/sessions table** — Phase 3/4 if domain requires it.

---

## Next step

**Phase 2 — Configuration and persistence** — await explicit go-ahead. Do not implement until requested.
