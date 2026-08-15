<div align="center">
    <h1>Laravel Analytics</h1>
</div>

<p align="center">
    <a href="https://github.com/simba-jirira-source/laravel-analytics/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/simba-jirira-source/laravel-analytics/tests.yml?branch=main&label=Tests&style=flat-square"></a>
</p>

## Status

Early development. The package foundation is in place; analytics tracking, dashboard, and related features are not implemented yet.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

You can install the package via Composer:

```bash
composer require simba-jirira-source/laravel-analytics
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="analytics"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="analytics-config"
```

### Publishing and Running the Migrations

```bash
php artisan vendor:publish --tag="analytics-migrations"
php artisan migrate
```

### Publishing the Views

```bash
php artisan vendor:publish --tag="analytics-views"
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="analytics-lang"
```

### Publishing the Public Assets

```bash
php artisan vendor:publish --tag="analytics-assets"
```

## Usage

<!-- Add a basic usage example here. -->

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Laravel Analytics! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [simba-jirira-source](https://github.com/simba-jirira-source)
- [All Contributors](../../contributors)

## License

Laravel Analytics is open-sourced software licensed under the [MIT license](LICENSE.md).
