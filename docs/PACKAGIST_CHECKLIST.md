# Packagist Checklist

Before publishing:

```powershell
composer validate --strict
composer verify
```

Confirm the repository is public, Composer metadata is final, the package name is available, PSR-4 autoloading and Laravel package discovery work, CI is green, the README installation command is correct, and Git history contains no secrets.

See [RELEASE_READINESS_REPORT.md](RELEASE_READINESS_REPORT.md) for the Phase 11 verification results.

Release sequence:

1. Merge release-ready code to `main`.
2. Review CHANGELOG.
3. Choose SemVer version.
4. Create and push tag (triggers `.github/workflows/release.yml`).
5. GitHub Release is created automatically when quality gates pass.
6. Register/verify package on Packagist.
7. Test `composer require simba-jirira-source/laravel-analytics` in a clean Laravel 13 app.

Never commit Packagist credentials or API tokens.
