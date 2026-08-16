# Security Policy

**Please do not disclose security vulnerabilities in public GitHub issues, discussions, or pull requests.**

## Supported versions

This package is pre-release. Security fixes are intended for the latest development state on the default branch until stable releases are tagged. Only tagged releases that explicitly appear in this policy will receive long-term support after `1.0.0`.

| Version | Supported |
| ------- | --------- |
| Unreleased / `main` | Yes |
| Tagged releases | Case-by-case until this table is updated at release time |

## Reporting a vulnerability

Preferred channel: **[GitHub private vulnerability reporting](https://github.com/simba-jirira-source/laravel-analytics/security/advisories/new)** for this repository (enable *Private vulnerability reporting* in repository settings if you maintain the project).

If private reporting is unavailable, contact the repository owner through GitHub (for example via a private security contact method listed on the maintainer profile). Do not open a public issue.

## What to include

Help us investigate quickly:

- Description of the issue and potential impact
- Steps to reproduce or a minimal proof of concept
- Affected package version or commit
- Laravel and PHP versions, if relevant
- Any suggested mitigation

Redact secrets, credentials, tokens, and personal data from reports.

## Response expectations

Maintainers will acknowledge reports as promptly as practical. Fixes may be coordinated through a private advisory before public disclosure. We appreciate responsible disclosure.

## Out of scope

The following are generally outside this package's security boundary:

- Host application misconfiguration (public dashboard without authorization, weak gates, exposed databases)
- Third-party services or infrastructure not shipped with this package
- Vulnerabilities in Laravel, Livewire, or other upstream dependencies (report those projects directly)

## Security-related configuration reminders

- Keep `analytics.dashboard.authorization` configured before enabling the dashboard.
- Keep IP banning disabled unless intentionally enabled.
- Review privacy settings before enabling tracking in production.
- Schedule `analytics:prune` explicitly; the package does not prune data automatically.
