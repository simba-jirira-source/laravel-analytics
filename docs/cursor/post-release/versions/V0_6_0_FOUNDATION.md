# Cursor Prompt — v0.6.0 Foundation, Portability and Performance

Act as a senior Laravel package maintainer.

Read completely:
- `docs/cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md`
- current `README.md`, `CHANGELOG.md`, `composer.json`
- all current GitHub Actions workflows
- current provider/config/routes/migrations/tests
- `docs/V1_READINESS_REPORT.md` if present

Implement **v0.6.0 only**. Do not implement v0.7+ functionality.

## Required objectives

1. Audit and resolve the public PHP namespace before 1.0.
2. Remove or justify any empty facade/public API.
3. Repair release/CHANGELOG automation.
4. Update README and Packagist-facing metadata to match actual release state.
5. Add focused MySQL and PostgreSQL integration CI alongside SQLite.
6. Test database-sensitive operations across supported DBs.
7. Improve pruning/performance only where measurements justify changes.
8. Add Composer security auditing.
9. Document every breaking pre-1.0 change.

If the package still uses `SimbaJirira\LaravelAnalytics\`, evaluate migration to `SimbaJirira\LaravelAnalytics\`. If migrating, update every source/test/workbench/doc reference and Composer PSR-4 metadata.

Inspect release workflows so CHANGELOG sections are not duplicated, release notes are useful, quality gates run before release, and stable tags correspond to approved `main` commits.

Add focused SQLite/MySQL/PostgreSQL tests for migrations, visitor uniqueness, page-view writes, error aggregation, dashboard date metrics, IP bans and pruning.

Review `analytics:prune`, dashboard aggregate queries, indexes and repeated metadata work. Implement chunking/caching/index changes only where justified.

Run:
```bash
composer validate --strict
composer audit
composer test
composer lint:check
composer analyse
```
plus database integration tests.

Update documentation and CHANGELOG. Create `docs/V0_6_0_READINESS_REPORT.md` containing namespace decision, breaking changes, database matrix, performance changes, tests, commands/results, risks and release recommendation.

Do not tag or publish v0.6.0. Stop after this version.
