# Data Retention and Pruning

Laravel Analytics stores traffic, visitor, error, and IP ban records in the host application's database. Retention is configurable and pruning is performed explicitly by the host application.

## Configuration

Retention settings live in `config/analytics.php`:

```php
'retention' => [
    'days' => 90,
    'prune_page_views' => true,
    'prune_visitors' => true,
    'prune_errors' => true,
    'prune_ip_bans' => true,
],
```

| Setting | Default | Behaviour |
|---------|---------|-----------|
| `days` | `90` | Records older than this cutoff are eligible for pruning |
| `prune_page_views` | `true` | Delete page views with `viewed_at` before the cutoff |
| `prune_visitors` | `true` | Delete visitors with `last_seen_at` before the cutoff when they have no retained page views |
| `prune_errors` | `true` | Delete aggregated errors with `last_occurred_at` before the cutoff |
| `prune_ip_bans` | `true` | Deactivate expired bans and remove old expired/inactive ban records |

Each prune toggle can be disabled independently without affecting the others.

## Manual pruning

Run the Artisan command:

```bash
php artisan analytics:prune
```

Override the retention window for a single run:

```bash
php artisan analytics:prune --days=30
```

The command is idempotent. Running it repeatedly against the same dataset removes nothing additional once eligible records have already been pruned.

## Scheduling in a host application

The package does **not** register a schedule automatically. Add pruning to the host application's scheduler explicitly, for example in `routes/console.php` or `app/Console/Kernel.php` depending on your Laravel version:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('analytics:prune')->daily();
```

Choose a cadence that matches your retention policy and database size. Daily pruning is a common starting point for the default 90-day retention window.

Because pruning is destructive, keep the schedule under version control and review retention settings before enabling it in production.

## Safety notes

- Pruning uses configured cutoffs only; it does not truncate entire tables.
- Visitor pruning skips visitors that still have page views inside the retention window.
- Expired IP bans are deactivated before old ban records are removed.
- Disabling a prune toggle leaves that record type untouched while other pruning continues.
