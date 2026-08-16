# Cursor Prompt — v1.0.0 Stable API Hardening

Act as the senior maintainer responsible for the first stable release.

Read the post-release plan, `docs/V0_9_0_RELEASE_CANDIDATE_REPORT.md`, `docs/PUBLIC_API.md`, current README/config/docs, composer metadata, migrations, contracts, API/facade, commands, routes, middleware, Livewire components, workflows and CHANGELOG.

Perform **1.0 final hardening only**. Do not add major features.

Audit/finalize Composer package name, PHP namespace, provider, facade/API, contracts, config keys, middleware aliases, Artisan commands, publish tags, public events, dashboard routes and documented extension points. Remove accidental public API before release.

Confirm and document only CI-tested PHP/Laravel/SQLite/MySQL/PostgreSQL/Livewire combinations.

Perform security regression review of dashboard and Livewire authorization, export authorization, IP-ban behaviour and bypasses, proxy/client-IP assumptions, error redaction, event-property limits, UTM sanitization, XSS escaping, SQL injection safety, command validation and secret leakage.

Verify privacy docs exactly match runtime defaults and explain host responsibility for custom-event data. Make no legal compliance claims.

Review migrations, indexes, upgrade migrations, pruning, high-cardinality columns, event growth, error aggregation, exports, dashboard queries and cross-driver SQL.

Finalize README, INSTALLATION, CONFIGURATION, PUBLIC_API, UPGRADING, PRIVACY, SECURITY, DASHBOARD, CUSTOM_EVENTS, EXPORTS, TROUBLESHOOTING and CHANGELOG.

Run:
```bash
composer validate --strict
composer audit
composer verify
```
plus full compatibility/database test suites and benchmark smoke checks.

Create `docs/V1_FINAL_RELEASE_REPORT.md` containing exact support matrix, stable public API, quality/database results, security/privacy review, performance summary, known limitations, blockers and release recommendation.

Do not create the v1.0.0 tag, GitHub Release or Packagist publication. Wait for explicit maintainer approval and stop.
