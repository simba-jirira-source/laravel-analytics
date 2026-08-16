# Visitor Identification

Laravel Analytics derives visitor identity using a replaceable `VisitorIdentifier` contract. The default implementation never treats a raw IP address alone as a permanent identity.

## Default strategy

The bundled `DefaultVisitorIdentifier` builds a one-way SHA-256 hash from intentionally selected request metadata:

1. Application-specific salt (`analytics.privacy.hash_salt`, falling back to `app.key`)
2. Normalized client IP address (IPv4 or IPv6)
3. User agent string when `analytics.privacy.collect_user_agent` is enabled
4. Authenticated user identifier when `analytics.privacy.track_authenticated_users` is enabled

The resulting `visitor_hash` is stored on both `analytics_visitors` and `analytics_page_views`.

Optional IP storage on the visitor record:

- `analytics.privacy.store_raw_ip` controls whether the normalized IP is persisted in `ip_address`
- `analytics.privacy.hash_ips` controls whether a separate `ip_hash` column is populated

No cookies are used for visitor identification.

## Unique vs repeat visitors

- **Unique visitors** — count of distinct `analytics_visitors` rows in a period
- **Repeat visitors** — visitors with two or more recorded page views in a period

These counts are approximate operational metrics, not mathematically perfect audience measurements.

## Replacing the identifier

Publish and customize configuration, then point `analytics.visitor_identifier` at your own class:

```php
'visitor_identifier' => App\Analytics\CustomVisitorIdentifier::class,
```

Your class must implement `LaravelAnalytics\LaravelAnalytics\Contracts\VisitorIdentifier`.

## Known limitations

Visitor counts can be affected by:

- NAT and shared networks (many devices behind one public IP)
- VPNs and mobile networks that rotate addresses
- Changing user agents (browser updates, privacy tools)
- Shared devices and browsers
- Reverse proxies when trusted proxy configuration is incorrect
- Bots and automated traffic (ignored user-agent filtering is not implemented in v1)
- Authenticated users who browse logged out and logged in separately (unless user tracking is enabled)

This package provides technical privacy controls but does not itself make an application compliant with any specific privacy law or regulatory framework.
