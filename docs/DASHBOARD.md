# Dashboard

The optional analytics dashboard is built with **Livewire 4** and **Blade** views using Tailwind-compatible utility classes. It does not require AdminLTE, Bootstrap, React, Vue, Inertia, or jQuery.

## Activation requirements

All of the following must be true:

1. `analytics.dashboard.enabled` is `true`
2. `analytics.dashboard.authorization` is a non-empty gate name or invokable class
3. The host application provides working authentication when `auth` middleware is used

If authorization is `null`, dashboard routes are **not registered**.

## Routes

Default paths (configurable via `dashboard.path` and `dashboard.route_prefix`):

| Method | Path | Route name | Component |
|--------|------|------------|-----------|
| GET | `/analytics` | `analytics.dashboard` | `AnalyticsDashboard` |
| GET | `/analytics/errors/{error}` | `analytics.errors.show` | `ErrorDetails` |

Middleware stack:

1. Values from `dashboard.middleware` (default `web`, `auth`)
2. `analytics.dashboard` (package authorization)

## Authorization

Configure a gate name:

```php
'dashboard' => [
    'authorization' => 'viewAnalyticsDashboard',
],
```

```php
Gate::define('viewAnalyticsDashboard', fn ($user) => $user?->is_admin ?? false);
```

Or an invokable class:

```php
'use SimbaJirira\LaravelAnalytics\Support\AllowAuthenticatedDashboardAccess;

'dashboard' => [
    'authorization' => AllowAuthenticatedDashboardAccess::class,
],
```

`DashboardAuthorizer` resolves invokable classes from the container and calls them with the current user.

Unauthorized access returns HTTP 403.

## Livewire components

Registered under the `analytics.*` component names:

| Component | Purpose |
|-----------|---------|
| `analytics.analytics-dashboard` | Shell with date filters |
| `analytics.traffic-overview` | KPI cards |
| `analytics.traffic-chart` | Traffic trend (CSS bar chart) |
| `analytics.top-pages` | Top pages table |
| `analytics.top-referrers` | Top referrers table |
| `analytics.status-breakdown` | HTTP status breakdown |
| `analytics.recent-errors` | Paginated recent errors |
| `analytics.error-details` | Single error detail view |
| `analytics.ip-ban-manager` | Ban list and ban/unban actions |

## Filters and pagination

- Main dashboard syncs `from` and `to` date query parameters (validated on apply).
- Recent errors and IP bans paginate using `dashboard.pagination.per_page` (default 25).
- Child components use `DashboardDateRange::resolveOrDefault()` during filter editing.

## IP ban management

Authorized dashboard users can ban and unban exact IPv4/IPv6 addresses through `IpBanManager`. Validation and authorization apply to mutating actions.

CLI recovery remains available:

```bash
php artisan analytics:ip-unban 203.0.113.10
```

## Customizing views

Publish views:

```bash
php artisan vendor:publish --tag=analytics-views
```

Published path: `resources/views/vendor/analytics/`.

Layouts:

- `analytics::layouts.dashboard` — package dashboard layout
- Livewire views under `analytics::livewire.*`

## Linking from your application

The package does not inject navigation into host applications. Add your own link, for example:

```blade
@can('viewAnalyticsDashboard')
    <a href="{{ route('analytics.dashboard') }}">Analytics</a>
@endcan
```

Adjust the gate name to match your configuration.

## Self-tracking

Dashboard paths are listed in `analytics.ignored` by default so analytics routes do not record themselves.

If you change `dashboard.path`, update `ignored.paths` accordingly.

## Dependencies

Livewire 4 is a runtime Composer dependency of this package. The host application must publish Livewire assets according to Livewire's installation guide if not already configured.

## Related documentation

- [INSTALLATION.md](INSTALLATION.md)
- [CONFIGURATION.md](CONFIGURATION.md)
- [ARCHITECTURE.md](ARCHITECTURE.md)
