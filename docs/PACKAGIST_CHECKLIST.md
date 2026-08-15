# Packagist Checklist

Before publishing:

```powershell
composer validate --strict
composer test
composer lint
composer analyse
```

Confirm the repository is public, Composer metadata is final, the package name is available, PSR-4 autoloading and Laravel package discovery work, CI is green, the README installation command is correct, and Git history contains no secrets.

Release sequence:
1. Merge release-ready code to `main`.
2. Review CHANGELOG.
3. Choose SemVer version.
4. Create and push tag.
5. Create GitHub Release.
6. Register/verify package on Packagist.
7. Test `composer require <vendor>/laravel-analytics` in a clean Laravel 13 app.

Never commit Packagist credentials or API tokens.
