# Laravel Analytics — Implementation Plan

> **Status:** Phase 7 complete (retention and maintenance). No dashboard.
>
> **Last updated:** 2026-08-16
>
> **Package:** `simba-jirira-source/laravel-analytics` · **Namespace:** `LaravelAnalytics\LaravelAnalytics`

---

## Phase 7 Report

### What was implemented

Phase 7 added configurable, idempotent analytics retention pruning:

- **`AnalyticsPruner`** — prunes page views, visitors, errors, and IP ban records according to `analytics.retention` settings.
- **`analytics:prune` command** — reports per-type removal counts; supports optional `--days` override.
- **`analytics.retention.prune_ip_bans`** — deactivates expired bans and removes old expired/inactive ban records.
- **`docs/RETENTION.md`** — retention configuration reference and host-application scheduling guidance.

**Not implemented (by design):** automatic schedule registration, dashboard UI, Phase 8+ features.

---

### Retention behaviour

| Record type | Cutoff field | Prune toggle |
|-------------|--------------|--------------|
| Page views | `viewed_at` | `prune_page_views` |
| Visitors | `last_seen_at` (only when no retained page views remain) | `prune_visitors` |
| Errors | `last_occurred_at` | `prune_errors` |
| IP bans | `expires_at` / inactive `banned_at` | `prune_ip_bans` |

Default retention window: **90 days**. Each prune toggle can be disabled independently.

---

### Safety guarantees

| Guarantee | Mechanism |
|-----------|-----------|
| Configurable | Per-type toggles and `days` setting in config; `--days` CLI override |
| Idempotent | Second run removes zero additional eligible records |
| Safe to repeat | Cutoff-based deletes only; no table truncation |
| No silent scheduling | Package does not register Laravel schedules |
| Visitor integrity | Visitors with retained page views are not removed |

---

### Files created or changed

| Action | Path |
|--------|------|
| Created | `src/Services/AnalyticsPruner.php` |
| Created | `src/Console/Commands/AnalyticsPruneCommand.php` |
| Created | `docs/RETENTION.md` |
| Created | `tests/RetentionTestCase.php` |
| Created | `tests/Database/AnalyticsPrunerTest.php` |
| Created | `tests/Retention/AnalyticsPruneCommandTest.php` |
| Modified | `config/analytics.php` |
| Modified | `src/LaravelAnalyticsServiceProvider.php` |
| Modified | `tests/Pest.php` |
| Modified | `tests/Unit/ConfigTest.php` |
| Modified | `phpunit.xml.dist` |
| Modified | `CHANGELOG.md` |
| Modified | `docs/IMPLEMENTATION_PLAN.md` |

---

### Tests added

| File | Coverage |
|------|----------|
| `tests/Database/AnalyticsPrunerTest.php` | Cutoff pruning, visitor safety, IP ban maintenance, toggles, idempotency, custom days |
| `tests/Retention/AnalyticsPruneCommandTest.php` | CLI execution, `--days` override, invalid input, repeated runs |

**Suite totals after Phase 7:** 129 tests, 287 assertions.

---

### Commands run

```powershell
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
| Pest test suite | **129 passed** (6255 ms) |

---

### Scheduling documentation

Host applications should register pruning explicitly, for example:

```php
Schedule::command('analytics:prune')->daily();
```

See [`docs/RETENTION.md`](RETENTION.md) for full configuration and safety notes.

---

### Remaining risks

| Item | Status |
|------|--------|
| Large datasets may need chunked pruning in future | Acceptable for initial release |
| Host must choose appropriate schedule cadence | Documented |
| Retention does not require `analytics.enabled` | Allows cleanup when tracking is off |

**No blockers prevent Phase 8.**

---

### Phase 8 readiness

| Prerequisite | Status |
|--------------|--------|
| Analytics data pipeline complete | Ready |
| Retention/maintenance command available | Ready |
| Dashboard config scaffold exists | Ready (disabled) |
| Authorization config placeholder exists | Ready |

**Phase 8 scope:** Livewire dashboard — **not started** (await explicit go-ahead).

---

## Phase 6 Report (archived summary)

Opt-in IP banning with IPv4/IPv6 exact-match support, middleware enforcement, and CLI recovery. See git history for full details.

---

## Current state (summary)

Traffic, visitor, error, and IP ban features operational when enabled. Configurable retention pruning via `analytics:prune`. No dashboard.

---

## Implementation phases

| Phase | Scope | Status |
|-------|-------|--------|
| **0–6** | Discovery → IP banning | Complete |
| **7** | Retention and maintenance | **Complete** |
| **8–12** | Dashboard, OSS, CI, release, hardening | Pending |

---

## Next step

**Phase 8 — Livewire dashboard** — await explicit go-ahead. Do not begin without instruction.
