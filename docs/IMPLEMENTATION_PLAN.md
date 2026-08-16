# Laravel Analytics — Implementation Plan

> **Status:** Phase 4 complete (visitor analytics). No error tracking, IP banning, or dashboard.
>
> **Last updated:** 2026-08-16
>
> **Package:** `simba-jirira-source/laravel-analytics` · **Namespace:** `LaravelAnalytics\LaravelAnalytics`

---

## Phase 4 Report

### What was implemented

Phase 4 expanded visitor analytics and privacy-aware identification while preserving the Phase 3 traffic pipeline:

- **`VisitorService`** — upserts visitors, preserves `first_seen_at`, updates `last_seen_at`, applies privacy rules for IP/UA/user association.
- **`VisitorAnalytics`** — unique/repeat visitor counting and repeat detection via page view activity.
- **`DefaultVisitorIdentifier` (enhanced)** — salt + normalized IP + optional UA + optional authenticated user ID; never IP alone.
- **`IpAddressNormalizer`** — IPv4, IPv6, and IPv4-mapped IPv6 normalization.
- **`AnalyticsHashSalt`** — resolves `analytics.privacy.hash_salt` or `app.key`.
- **`analytics.visitor_identifier` config** — replaceable identifier binding.
- **`docs/VISITOR_IDENTIFICATION.md`** — strategy, replacement instructions, known limitations.

**Not implemented (by design):** error tracking, IP ban middleware, dashboard, bot filtering, cookies, queue-backed recording.

---

### Visitor identification strategy

The default identifier produces a one-way SHA-256 hash from:

1. Application salt (`analytics.privacy.hash_salt` → `app.key` fallback)
2. Normalized client IP (IPv4 / IPv6 / mapped IPv6)
3. User agent (when `analytics.privacy.collect_user_agent` is true)
4. Authenticated user ID (when `analytics.privacy.track_authenticated_users` is true)

No cookies. Raw inputs are not stored in the hash. Replace via `analytics.visitor_identifier` implementing `VisitorIdentifier`.

**Unique vs repeat:**

- **Unique** — distinct `analytics_visitors` rows
- **Repeat** — visitors with ≥ 2 page views

See [`docs/VISITOR_IDENTIFICATION.md`](VISITOR_IDENTIFICATION.md).

---

### Privacy decisions

| Setting | Default | Behaviour |
|---------|---------|-----------|
| `store_raw_ip` | `false` | `ip_address` null unless explicitly enabled |
| `hash_ips` | `true` | Separate `ip_hash` on visitor when enabled |
| `hash_salt` | `null` | Falls back to `app.key` |
| `collect_user_agent` | `true` | UA in hash + optional visitor storage |
| `track_authenticated_users` | `false` | User ID omitted from hash and records unless enabled |

Page views inherit `user_id` from the resolved visitor record. No request bodies, cookies, tokens, or auth headers are persisted.

---

### Files created or changed

| Action | Path |
|--------|------|
| Created | `src/Services/VisitorService.php` |
| Created | `src/Services/VisitorAnalytics.php` |
| Created | `src/Support/AnalyticsHashSalt.php` |
| Created | `src/Support/IpAddressNormalizer.php` |
| Created | `docs/VISITOR_IDENTIFICATION.md` |
| Created | `tests/Unit/IpAddressNormalizerTest.php` |
| Created | `tests/Database/VisitorAnalyticsTest.php` |
| Created | `tests/Tracking/VisitorTrackingTest.php` |
| Modified | `src/Support/DefaultVisitorIdentifier.php` |
| Modified | `src/Services/PageViewRecorder.php` |
| Modified | `src/LaravelAnalyticsServiceProvider.php` |
| Modified | `config/analytics.php` |
| Modified | `tests/Unit/DefaultVisitorIdentifierTest.php` |
| Modified | `CHANGELOG.md` |
| Modified | `docs/IMPLEMENTATION_PLAN.md` |

---

### Tests added

| File | Coverage |
|------|----------|
| `tests/Unit/IpAddressNormalizerTest.php` | IPv4, IPv6, mapped IPv6, empty IP |
| `tests/Unit/DefaultVisitorIdentifierTest.php` | Stable hash, IP/UA variants, salt, auth user, IPv4/IPv6 |
| `tests/Database/VisitorAnalyticsTest.php` | Unique/repeat upsert, raw IP toggle, count queries |
| `tests/Tracking/VisitorTrackingTest.php` | End-to-end repeat/unique visitors, raw IP privacy defaults |

**Suite totals after Phase 4:** 68 tests, 158 assertions.

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
| Pest test suite | **68 passed** (7665 ms) |

---

### Remaining risks

| Item | Status |
|------|--------|
| Visitor counts are approximate | Documented in `VISITOR_IDENTIFICATION.md` |
| No bot/user-agent ignore list | Future enhancement |
| Auth user only when logged in during request | Documented limitation |
| Trusted proxy misconfiguration | Host app responsibility |
| `VisitorAnalytics` period filters basic | Dashboard may need richer queries in Phase 8 |

**No blockers prevent Phase 5.**

---

### Phase 5 readiness

| Prerequisite | Status |
|--------------|--------|
| Traffic + visitor pipeline stable | Ready |
| Page views linked to visitors | Ready |
| Privacy defaults enforced | Ready |
| Extension contracts in place | Ready |
| Error table/model exists | Ready (unused) |
| Middleware pattern established | Ready for error middleware |

**Phase 5 scope:** safe HTTP error recording, fingerprint aggregation, rethrow guarantee, sensitive data exclusion tests.

---

## Phase 3 Report (archived summary)

Traffic tracking via `TrackTrafficMiddleware` and `PageViewRecorder`. See git history for details.

---

## Current state (summary)

Traffic and visitor analytics operational when enabled. Privacy-aware hashed identifiers, unique/repeat visitor metrics, optional raw IP storage. No error tracking, IP banning, or dashboard.

---

## Implementation phases

| Phase | Scope | Status |
|-------|-------|--------|
| **0–3** | Discovery → traffic analytics | Complete |
| **4** | Visitor analytics | **Complete** |
| **5** | Error analytics | Next |
| **6–12** | Bans, retention, dashboard, OSS, CI, release, hardening | Pending |

---

## Next step

**Phase 5 — Error analytics** — await explicit go-ahead.
