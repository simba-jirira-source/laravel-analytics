# Contributing to Laravel Analytics

Thank you for considering a contribution. This package is a reusable Laravel library; changes should stay focused, tested, and backwards-compatible unless explicitly discussed.

## Prerequisites

- PHP 8.3+
- Composer 2
- Git

You do not need a full Laravel application in this repository. Development uses [Orchestra Testbench](https://packages.tools/testbench) and the bundled workbench.

## Getting started

1. Fork the repository on GitHub.
2. Clone your fork locally.
3. Install dependencies:

```bash
composer install
```

4. Create a feature branch from `main`.

## Development workflow

### Continuous integration

GitHub Actions runs separate workflows on pull requests and pushes to `main` / `*.x` branches:

| Workflow | File | Purpose |
|----------|------|---------|
| **Tests** | `.github/workflows/tests.yml` | Pest suite + type coverage across PHP 8.3–8.5, Laravel 12/13, prefer-lowest/stable, Ubuntu and Windows |
| **Static Analysis** | `.github/workflows/static-analysis.yml` | PHPStan (level 7) across the same PHP/Laravel/stability matrix on Ubuntu |
| **Code Style** | `.github/workflows/code-style.yml` | Pint (`composer lint:check`) |

Tagged releases (`v*`) trigger `.github/workflows/release.yml`, which runs the full quality gates before creating a GitHub Release. Packagist publication is not automated from this repository.

Dependabot opens weekly update PRs for Composer and GitHub Actions (see `.github/dependabot.yml`).

### Run the full verification suite

```bash
composer verify
```

This runs, in order:

- `composer validate --strict`
- `composer run prepare` (Testbench package discovery)
- PHPStan (`composer analyse`, level 7)
- Pint (`composer lint:check`)
- Pest type coverage (100% minimum)
- Pest test suite (parallel on non-Windows CI; locally may vary)

### Individual commands

```bash
composer test:unit     # Pest only
composer analyse       # PHPStan (run prepare first)
composer lint          # Fix formatting
composer lint:check    # Check formatting
composer build         # Build workbench
```

### Workbench

```bash
composer build
composer serve
```

## Code expectations

- Follow existing package conventions and Laravel-native APIs.
- Match the coding style enforced by Pint (`pint.json`, Laravel preset).
- Keep public APIs explicit; avoid advertising internal classes as extension points.
- Prefer dependency injection over static helpers.
- Do not weaken tests or static-analysis rules to make CI green.

## Tests

- Behaviour changes require Pest tests.
- Bug fixes require regression tests.
- Focus on observable package behaviour through public APIs, middleware, commands, config, routes, and published resources.
- Avoid meaningless assertions.

Test directories map to concerns (`tests/Tracking`, `tests/Dashboard`, etc.). Use the appropriate `*TestCase` base class when adding feature tests.

## Documentation

Update documentation when behaviour, configuration, or public APIs change:

- `README.md` for user-facing overview
- `CHANGELOG.md` under **Unreleased**
- Relevant files in `docs/` (installation, configuration, privacy, architecture, dashboard)

Do not fabricate releases, download counts, compatibility claims, or security contacts.

## Pull requests

Before opening a PR:

1. Ensure `composer verify` passes locally when possible.
2. Update `CHANGELOG.md` under **Unreleased**.
3. Describe motivation, implementation, and test coverage.
4. Note any backwards-compatibility impact.

Use the pull request template when available.

For significant changes, open an issue first to discuss approach and scope.

## Issues

Use GitHub issue forms when available:

- **Bug reports** — include package, Laravel, and PHP versions, reproduction steps, and expected vs actual behaviour. Redact secrets from logs.
- **Feature requests** — describe the problem, proposed behaviour, and compatibility considerations.

## Backwards compatibility

Public configuration keys, contracts, Artisan command signatures, and documented middleware aliases should remain stable within a major version. Breaking changes require discussion and a major version bump per [Semantic Versioning](https://semver.org/).

## Code of conduct

This project follows the [Contributor Covenant](../CODE_OF_CONDUCT.md). Participate respectfully.

## Security

Do not report security vulnerabilities in public issues. See [SECURITY.md](SECURITY.md).
