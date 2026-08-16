# Releases

Maintainer guide for tagging and publishing Laravel Analytics.

**Cursor and CI automation must not decide when to publish a release.** The maintainer reviews readiness, prepares the changelog, and creates the annotated version tag.

## Release flow

```text
development
    ↓
quality gates (local + CI)
    ↓
merge to main
    ↓
quality gates on main
    ↓
prepare CHANGELOG (move [Unreleased] → version section + date)
    ↓
annotated version tag (maintainer action)
    ↓
push tag
    ↓
GitHub Actions release workflow
    ↓
GitHub Release (from CHANGELOG section)
    ↓
Packagist synchronization (webhook or manual)
```

## Pre-release checklist

Before tagging:

1. Ensure `main` is green in GitHub Actions (tests, type coverage, static analysis, code style, database, security).
2. Run locally when possible:

```bash
composer validate --strict
composer verify
```

3. Review [CHANGELOG.md](../CHANGELOG.md) — move **Unreleased** entries into a version section with the release date.
4. Confirm [README.md](../README.md) installation command matches `simba-jirira-source/laravel-analytics`.
5. Confirm no secrets, `.env` files, or credentials are committed.
6. Review [docs/PRIVACY.md](PRIVACY.md) and [docs/CONFIGURATION.md](CONFIGURATION.md) for accuracy.

See also [OSS_RELEASE_CHECKLIST.md](OSS_RELEASE_CHECKLIST.md) and [PACKAGIST_CHECKLIST.md](PACKAGIST_CHECKLIST.md).

## Version numbering

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** — incompatible public API changes
- **MINOR** — backwards-compatible functionality
- **PATCH** — backwards-compatible bug fixes

Before `1.0.0`, minor versions may include breaking changes if documented in the changelog.

## Tagging workflow

1. Merge all release changes to `main`.
2. Choose the version (example: `v0.6.1`).
3. Update `CHANGELOG.md` with the release date and version heading.
4. Commit the changelog update.
5. Create an annotated tag:

```bash
git tag -a v0.6.1 -m "Release v0.6.1"
git push origin v0.6.1
```

Pushing a `v*` tag triggers [.github/workflows/release.yml](../.github/workflows/release.yml), which:

1. Verifies the tagged commit is reachable from `origin/main`
2. Runs `composer validate --strict`, `composer audit`, PHPStan, Pint, type coverage, and Pest
3. Creates a GitHub Release using the matching `CHANGELOG.md` section (not auto-generated release notes)

Pre-release tags containing `-alpha`, `-beta`, or `-rc` are marked as GitHub pre-releases automatically.

6. If using Packagist, verify the new tag is indexed.
7. Smoke-test in a fresh Laravel application:

```bash
composer require simba-jirira-source/laravel-analytics:^0.6
```

## CI architecture notes

- **Compatibility matrix** (`.github/workflows/tests.yml`): PHP 8.3–8.5 × Laravel 12/13 × prefer-lowest/stable × Ubuntu/Windows — runs Pest behaviour tests only; `fail-fast: false`.
- **Type coverage**: dedicated stable job (PHP 8.4, Laravel 13, prefer-stable) — runs `composer test:types` once.
- **Database integration** (`.github/workflows/database.yml`): SQLite, MySQL, PostgreSQL.
- **Security** (`.github/workflows/security.yml`): `composer validate --strict` and `composer audit`.

## Packagist

Packagist publication requires:

- Public GitHub repository
- Valid `composer.json` (`composer validate --strict`)
- Packagist account and maintainer approval
- GitHub webhook or manual update configured on Packagist

Do not embed Packagist API tokens in this repository.

## GitHub repository metadata

Recommended description, topics, and homepage: [GITHUB_REPOSITORY_SETUP.md](GITHUB_REPOSITORY_SETUP.md).

## Post-release

- Monitor GitHub Actions on `main`
- Triage issues and security advisories
- Begin a new empty **Unreleased** section in `CHANGELOG.md` for the next cycle

## Related documentation

- [CHANGELOG.md](../CHANGELOG.md)
- [GITHUB_REPOSITORY_SETUP.md](GITHUB_REPOSITORY_SETUP.md)
- [phases/PHASE_11_PACKAGIST_RELEASE.md](phases/PHASE_11_PACKAGIST_RELEASE.md)
