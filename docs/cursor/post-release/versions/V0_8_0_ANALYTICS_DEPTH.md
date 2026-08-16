# Cursor Prompt — v0.8.0 Analytics Depth and Export

Act as a senior Laravel package maintainer.

Read the post-release improvement plan and current repository/docs.

Implement **v0.8.0 only**. Do not implement v0.9/1.0 work.

Add:
1. optional browser/device/OS metadata derived from already-enabled user-agent collection;
2. deterministic configurable bot filtering with documented limitations;
3. allow-listed UTM tracking for only `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`;
4. current-versus-previous period comparisons with safe zero/missing handling;
5. authorized CSV/JSON exports using chunking/streaming rather than large in-memory buffers.

Do not implement invasive fingerprinting. Do not store arbitrary query parameters. Preserve referrer/query privacy behaviour.

Exports must validate filters, enforce dashboard authorization and omit hidden/sensitive fields.

Add tests for metadata parsing, bot filtering, UTM allow-listing, query privacy regressions, comparison math, export authorization, large-export strategy and cross-database queries where relevant.

Update README, CONFIGURATION, PRIVACY, DASHBOARD and CHANGELOG. Create `docs/V0_8_0_READINESS_REPORT.md`.

Run all quality gates. Do not tag or publish. Stop after v0.8.0.
