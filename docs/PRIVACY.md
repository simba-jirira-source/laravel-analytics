# Privacy

This document describes what Laravel Analytics records, what it excludes by default, and which settings increase or decrease data collection.

> **This package provides technical privacy controls but does not itself make an application compliant with any specific privacy law or regulatory framework.**

## Default posture

Out of the box (merged config defaults):

| Capability | Default |
|------------|---------|
| Package master switch | Off |
| Traffic tracking | Off |
| Error tracking | Off |
| IP banning | Off |
| Dashboard | Off |
| Raw IP storage | Off |
| Authenticated user association | Off |
| Referrer collection | On (when tracking is enabled) |
| User-agent collection | On (when tracking is enabled) |

Nothing is recorded until you enable `analytics.enabled` and the relevant tracking toggles.

## Traffic and visitor data

When traffic tracking is enabled, the package may store:

| Field | Source | Notes |
|-------|--------|-------|
| `visitor_hash` | Derived identifier | One-way hash; see [VISITOR_IDENTIFICATION.md](VISITOR_IDENTIFICATION.md) |
| `path`, `method`, `route_name` | HTTP request | |
| `referrer_host`, `referrer_url` | Referer header | When `privacy.collect_referrer` is true |
| `status_code`, `duration_ms` | Response | |
| `viewed_at` | Request timestamp | |
| `user_id` | Authenticated user | Only when `privacy.track_authenticated_users` is true |
| `ip_address` | Client IP | Only when `privacy.store_raw_ip` is true |
| `ip_hash` | Hashed IP | When `privacy.hash_ips` is true |

Visitor identification uses a configurable salt (`privacy.hash_salt` or `app.key`). No cookies are set for identification.

## Error analytics

When error tracking is enabled, the package records **safe diagnostic metadata** for HTTP-layer exceptions:

- Exception class
- Sanitized message (patterns such as `password=`, `token=`, and `authorization=` are redacted)
- Route name, path, HTTP method
- Status code (when available)
- File and line
- Fingerprint for grouping repeat errors
- First / last occurrence timestamps and occurrence count

The package does **not** persist:

- Request bodies
- Passwords, cookies, sessions, or bearer tokens
- Authorization headers or API keys
- Uploaded file contents
- Environment variables or full configuration dumps
- Arbitrary request headers

Error recording runs inside middleware, rethrows the original exception, and swallows failures in the analytics recorder so application error handling is preserved.

Error tracking covers supported HTTP request paths handled by the middleware stack. It is not a replacement for APM, queue monitoring, or centralized logging.

## IP banning data

When IP banning is enabled, the package stores:

- Exact IPv4 or IPv6 address
- Optional reason
- Active flag and optional expiry timestamp
- Created / updated timestamps

Ban enforcement is **opt-in** (`ip_banning.enabled`). Installing the package does not block traffic by default.

## Dashboard access

The dashboard is disabled unless both `dashboard.enabled` and `dashboard.authorization` are configured. Unauthorized users receive HTTP 403.

Dashboard users who can access ban management can create and remove bans. Restrict authorization accordingly.

## Data retention and backups

Retention defaults to 90 days. Old records are removed only when the host application runs:

```bash
php artisan analytics:prune
```

The package does not schedule pruning automatically.

Database backups that include analytics tables will contain whatever data your configuration allows. Review retention and privacy settings before backing up production databases.

## Increasing collection (review carefully)

| Setting | Effect |
|---------|--------|
| `privacy.store_raw_ip` => true | Stores normalized client IP on visitor records |
| `privacy.track_authenticated_users` => true | Stores `user_id` and includes user in visitor hash |
| `tracking.errors` => true | Stores exception metadata |
| `ip_banning.enabled` => true | Stores and enforces ban records |

## Decreasing collection

| Setting | Effect |
|---------|--------|
| `privacy.collect_referrer` => false | Omits referrer fields |
| `privacy.collect_user_agent` => false | Excludes UA from visitor hash |
| `ignored.paths` / `ignored.route_names` | Skips matching requests |
| `enabled` => false | Disables persistence entirely |

Replace `visitor_identifier` or `error_recorder` with custom implementations for advanced control.

## Host application responsibilities

- Provide lawful basis and notices for analytics in your application.
- Configure authorization for the dashboard and ban management.
- Secure database access and backups.
- Schedule pruning appropriate to your policy.
- Configure Laravel trusted proxies correctly when behind load balancers.

## Related documentation

- [VISITOR_IDENTIFICATION.md](VISITOR_IDENTIFICATION.md)
- [RETENTION.md](RETENTION.md)
- [CONFIGURATION.md](CONFIGURATION.md)
