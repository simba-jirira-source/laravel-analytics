# GitHub Repository Setup

Recommended GitHub repository metadata for discoverability. **Apply these settings manually** in the GitHub UI or via `gh` — do not commit secrets or automate remote changes from CI.

## Recommended description

```text
Self-hosted, privacy-conscious first-party analytics for Laravel with visitors, page views, error monitoring, IP controls and an optional Livewire dashboard.
```

## Recommended topics

```text
laravel
php
analytics
laravel-package
web-analytics
self-hosted
privacy
first-party-analytics
livewire
visitor-analytics
```

## Recommended homepage

Use the public Packagist package URL unless the project later receives its own documentation website:

```text
https://packagist.org/packages/simba-jirira-source/laravel-analytics
```

## Optional GitHub CLI commands

Run from a authenticated local environment with permission to edit the repository:

```bash
gh repo edit simba-jirira-source/laravel-analytics \
  --description "Self-hosted, privacy-conscious first-party analytics for Laravel with visitors, page views, error monitoring, IP controls and an optional Livewire dashboard." \
  --homepage "https://packagist.org/packages/simba-jirira-source/laravel-analytics"

gh repo edit simba-jirira-source/laravel-analytics \
  --add-topic laravel \
  --add-topic php \
  --add-topic analytics \
  --add-topic laravel-package \
  --add-topic web-analytics \
  --add-topic self-hosted \
  --add-topic privacy \
  --add-topic first-party-analytics \
  --add-topic livewire \
  --add-topic visitor-analytics
```

Verify the result:

```bash
gh repo view simba-jirira-source/laravel-analytics --json description,homepageUrl,repositoryTopics
```

## Additional repository settings

| Setting | Recommendation |
|---------|----------------|
| Visibility | Public |
| Issues | Enabled |
| Vulnerability reporting | Private reporting enabled |
| Branch protection | Required checks on `main` (tests, static analysis, code style, database, security) |
| Dependabot alerts | Enabled |
| Secret scanning | Enable where available |

See also [RELEASES.md](RELEASES.md) and [OSS_RELEASE_CHECKLIST.md](OSS_RELEASE_CHECKLIST.md).
