# Cursor Prompt — v0.9.0 Release Candidate and Operational Hardening

Act as a senior Laravel package maintainer.

Read the complete repository and post-release plan.

Implement **v0.9.0 only**. This is release-candidate stabilization. Do not add major new product features.

Verify fresh installs for declared Laravel/PHP support, including headless and dashboard-enabled installations. Validate package discovery, config publication, migrations, tracking, custom events, pruning and exports.

Create/update `docs/UPGRADING.md` covering pre-1.0 namespace/schema/config migrations.

Re-evaluate Livewire as a hard dependency. Record a decision to keep it required, make it optional, or split the dashboard into a companion package. Do not split packages without clear user/maintenance benefit.

Build repeatable benchmark fixtures for large page-view/event data, pruning and common dashboard queries. Keep expensive benchmarks outside routine CI if appropriate.

Consider a safe `analytics:status` command showing package version, DB driver, enabled features, retention and migration readiness. Never expose keys, salts, credentials or secrets.

Improve screenshots, examples, troubleshooting, architecture diagrams and contributor onboarding.

Create `docs/PUBLIC_API.md` listing what is intended to become stable in 1.0 and distinguish extension points from internal implementation classes.

Run all quality/database gates. Create `docs/V0_9_0_RELEASE_CANDIDATE_REPORT.md` with install verification, upgrades, benchmark summary, Livewire decision, public API candidate and 1.0 blockers.

Do not tag or publish. Stop after v0.9.0.
