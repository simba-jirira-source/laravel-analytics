# Dashboard Screenshots

No dashboard screenshots are committed to this repository yet. Do not link to missing image files in the README.

## Recommended captures

Capture these from a running workbench or host application with the dashboard enabled and authorized:

| # | Screen | Suggested filename |
|---|--------|--------------------|
| 1 | Analytics overview dashboard (KPI cards, date filter) | `docs/images/dashboard-overview.png` |
| 2 | Traffic trends chart | `docs/images/traffic-trends.png` |
| 3 | Top pages and top referrers | `docs/images/top-pages-referrers.png` |
| 4 | Error analytics (recent errors / detail view) | `docs/images/error-analytics.png` |
| 5 | IP ban management | `docs/images/ip-ban-management.png` |

## Filename convention

- Store PNG files under `docs/images/`.
- Use lowercase kebab-case filenames.
- Prefer 1400–1600 px width for README embedding.
- Avoid screenshots containing real user data, credentials, or production domains.

## README placement

After capturing `docs/images/dashboard-overview.png`, uncomment the screenshot block near the top of [README.md](../README.md):

```markdown
![Analytics dashboard overview](docs/images/dashboard-overview.png)
```

Additional screenshots can be linked from [docs/DASHBOARD.md](DASHBOARD.md) as needed.

## Capture workflow

1. Run `composer build` and `composer serve` from the package workbench, or enable the dashboard in a host Laravel app.
2. Seed representative page views, errors, and bans for meaningful charts.
3. Capture at a consistent viewport size.
4. Commit images under `docs/images/` and update README/DASHBOARD links.
