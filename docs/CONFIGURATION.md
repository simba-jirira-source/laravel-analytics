# Configuration

All settings are defined in `config/analytics.php`. Publish with:

```bash
php artisan vendor:publish --tag=analytics-config
```

Environment variables are not required; application config is the primary interface.

## Master switch

| Key | Default | Description |
|-----|---------|-------------|
| `enabled` | `false` | Master switch. When false, tracking middleware does not persist data. |

## Dashboard

| Key | Default | Description |
|-----|---------|-------------|
| `dashboard.enabled` | `false` | Enable dashboard routes |
| `dashboard.path` | `analytics` | URL prefix (without leading slash) |
| `dashboard.route_prefix` | `analytics.` | Route name prefix |
| `dashboard.middleware` | `['web', 'auth']` | Middleware stack before package authorization |
| `dashboard.authorization` | `null` | Gate name or invokable class; routes stay disabled when null |
| `dashboard.pagination.per_page` | `25` | Pagination size for list widgets |
| `dashboard.cache_ttl` | `300` | Cache TTL (seconds) for aggregate dashboard queries |

Routes register only when `dashboard.enabled` is true **and** `dashboard.authorization` is set.

Authorization options:

- **Gate name** — resolved via `Gate::forUser($user)->allows($name)`
- **Invokable class** — e.g. `LaravelAnalytics\LaravelAnalytics\Support\AllowAuthenticatedDashboardAccess`

See [DASHBOARD.md](DASHBOARD.md).

## Tracking

| Key | Default | Description |
|-----|---------|-------------|
| `tracking.traffic` | `false` | Record page views via middleware on the `web` group |
| `tracking.errors` | `false` | Record HTTP exceptions via middleware on the `web` group |

Both require `enabled` => true`.

Middleware aliases registered by the service provider:

| Alias | Class |
|-------|-------|
| `analytics.track-traffic` | `TrackTrafficMiddleware` |
| `analytics.record-errors` | `RecordErrorsMiddleware` |
| `analytics.enforce-ip-ban` | `EnforceIpBanMiddleware` |
| `analytics.dashboard` | `AuthorizeAnalyticsDashboard` |

## IP banning

| Key | Default | Description |
|-----|---------|-------------|
| `ip_banning.enabled` | `false` | Prepend ban enforcement middleware to the `web` group |
| `ip_banning.blocked_status` | `403` | HTTP status returned for banned clients |

Requires `enabled` => true.

Only **exact** IPv4 or IPv6 addresses are supported (no CIDR ranges in v1).

CLI:

```bash
php artisan analytics:ip-ban 203.0.113.10 --reason="Abuse" --days=7
php artisan analytics:ip-unban 203.0.113.10
```

## Trusted proxies

| Key | Default | Description |
|-----|---------|-------------|
| `trusted_proxies` | `null` | When null, client IP resolution relies on the host application's trusted proxy configuration |

## Privacy

| Key | Default | Description |
|-----|---------|-------------|
| `privacy.store_raw_ip` | `false` | Persist normalized IP on visitor records |
| `privacy.hash_ips` | `true` | Persist hashed IP column when applicable |
| `privacy.hash_salt` | `null` | Salt source; falls back to `app.key` |
| `privacy.track_authenticated_users` | `false` | Include authenticated user ID in visitor hash and page views |
| `privacy.collect_referrer` | `true` | Store referrer host and URL on page views |
| `privacy.collect_user_agent` | `true` | Include user agent in visitor identification |

See [PRIVACY.md](PRIVACY.md) and [VISITOR_IDENTIFICATION.md](VISITOR_IDENTIFICATION.md).

## Visitor identification

| Key | Default | Description |
|-----|---------|-------------|
| `visitor_identifier` | `DefaultVisitorIdentifier::class` | Class implementing `VisitorIdentifier` |

## Error recorder

| Key | Default | Description |
|-----|---------|-------------|
| `error_recorder` | `AnalyticsErrorRecorder::class` | Class implementing `ErrorRecorder` |

## User association

| Key | Default | Description |
|-----|---------|-------------|
| `user.model` | `null` | Optional Eloquent model class (not enforced by the package) |
| `user.foreign_key` | `user_id` | Column name on page views when user tracking is enabled |

The package does not assume a specific `User` model.

## Ignored requests

| Key | Default | Description |
|-----|---------|-------------|
| `ignored.paths` | `analytics`, `analytics/*` | Paths excluded from tracking and error recording |
| `ignored.route_names` | `analytics.*` | Named routes excluded |
| `ignored.methods` | `OPTIONS` | HTTP methods excluded |

Adjust when your dashboard path differs from `analytics`.

## Retention

| Key | Default | Description |
|-----|---------|-------------|
| `retention.days` | `90` | Age cutoff for pruning |
| `retention.prune_page_views` | `true` | Prune old page views |
| `retention.prune_visitors` | `true` | Prune stale visitors |
| `retention.prune_errors` | `true` | Prune old error aggregates |
| `retention.prune_ip_bans` | `true` | Deactivate expired bans and remove old records |

See [RETENTION.md](RETENTION.md).

## Excluded status codes

| Key | Default | Description |
|-----|---------|-------------|
| `excluded_status_codes` | `[]` | HTTP status codes that should not be recorded as page views |

## Example production configuration

```php
return [
    'enabled' => true,

    'dashboard' => [
        'enabled' => true,
        'authorization' => 'viewAnalyticsDashboard',
        'middleware' => ['web', 'auth'],
    ],

    'tracking' => [
        'traffic' => true,
        'errors' => true,
    ],

    'ip_banning' => [
        'enabled' => false,
    ],

    'privacy' => [
        'store_raw_ip' => false,
        'hash_ips' => true,
        'track_authenticated_users' => false,
    ],

    'retention' => [
        'days' => 90,
    ],
];
```

## Publish tags

| Tag | Publishes |
|-----|-----------|
| `analytics` | All resources below |
| `analytics-config` | Config file |
| `analytics-migrations` | Migrations |
| `analytics-views` | Blade views |
| `analytics-lang` | Translations |
| `analytics-assets` | Public assets |
