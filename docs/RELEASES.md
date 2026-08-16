# Releases

Maintainer guide for tagging and publishing Laravel Analytics. **Do not publish to Packagist until repository settings, credentials, and package name availability are confirmed.**

## Pre-release checklist

Before tagging:

1. Ensure `main` is green in GitHub Actions (tests workflow).
2. Run locally when possible:

```bash
composer validate --strict
composer verify
```

3. Review [CHANGELOG.md](../CHANGELOG.md) — move **Unreleased** entries into a version section.
4. Confirm [README.md](../README.md) installation command matches the final Composer package name (`simba-jirira-source/laravel-analytics`).
5. Confirm no secrets, `.env` files, or credentials are committed.
6. Review [docs/PRIVACY.md](PRIVACY.md) and [docs/CONFIGURATION.md](CONFIGURATION.md) for accuracy.

See also [OSS_RELEASE_CHECKLIST.md](OSS_RELEASE_CHECKLIST.md) and [PACKAGIST_CHECKLIST.md](PACKAGIST_CHECKLIST.md).

## Version numbering

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** — incompatible public API changes
- **MINOR** — backwards-compatible functionality
- **PATCH** — backwards-compatible bug fixes

Before `1.0.0`, minor versions may include breaking changes if documented in the changelog.

Planning milestones (not yet released):

| Version | Scope |
|---------|-------|
| 0.1.0 | Foundation + traffic |
| 0.2.0 | Visitor analytics |
| 0.3.0 | Error analytics |
| 0.4.0 | IP banning |
| 0.5.0 | Livewire dashboard |
| 0.6.0 | Privacy / retention hardening |
| 0.9.0 | Release candidate quality |
| 1.0.0 | Stable documented API |

## Tagging workflow

1. Merge all release changes to `main`.
2. Choose the version (example: `v0.5.0`).
3. Update `CHANGELOG.md` with the release date and version heading.
4. Commit the changelog update.
5. Create an annotated tag according to project policy:

```bash
git tag -a v0.5.0 -m "Release v0.5.0"
git push origin v0.5.0
```

Pushing a `v*` tag triggers `.github/workflows/release.yml`, which:

1. Runs `composer validate --strict`, PHPStan, Pint, type coverage, and Pest on Ubuntu (PHP 8.4, Laravel 13, prefer-stable)
2. Creates a GitHub Release with generated release notes (only if all gates pass)

Pre-release tags containing `-alpha`, `-beta`, or `-rc` are marked as GitHub pre-releases automatically.

6. The `Update Changelog` workflow (`.github/workflows/update-changelog.yml`) can commit release notes to `CHANGELOG.md` when a GitHub Release is published.
7. If using Packagist, verify the new tag is indexed (Phase 11).
8. Smoke-test in a fresh Laravel application:

```bash
composer require simba-jirira-source/laravel-analytics:^0.5
```

## Packagist

Packagist publication requires:

- Public GitHub repository
- Valid `composer.json` (`composer validate --strict`)
- Packagist account and maintainer approval
- GitHub webhook or manual update configured on Packagist

Do not embed Packagist API tokens in this repository. Use Packagist UI or documented CI secrets (Phase 11).

## GitHub repository settings (recommended)

These settings are configured in GitHub, not in code:

- Public repository
- Issues enabled
- Private vulnerability reporting enabled
- Branch protection / rulesets on `main`
- Required status checks (tests workflow)
- Pull requests required before merge
- Dependabot alerts enabled
- Secret scanning where available

## Post-release

- Monitor GitHub Actions on `main`
- Triage issues and security advisories
- Begin **Unreleased** section in `CHANGELOG.md` for the next cycle

## Related documentation

- [CHANGELOG.md](../CHANGELOG.md)
- [phases/PHASE_11_PACKAGIST_RELEASE.md](phases/PHASE_11_PACKAGIST_RELEASE.md)
