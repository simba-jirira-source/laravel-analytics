# Laravel Analytics — Cursor Development Pack

Use this pack with Laravel's official package skeleton to build `laravel-analytics` incrementally as a public Composer/Laravel package.

## Start

```powershell
cd E:\projects
laravel package laravel-analytics
cd laravel-analytics
cursor .
```

Copy this pack into the repository, preferably under `docs/cursor/`.

## Prompt order

1. `00_LARAVEL_AGENT_BOOTSTRAP.md`
2. `CURSOR_LARAVEL_ANALYTICS_MASTER_PROMPT.md`
3. `phases/PHASE_00_DISCOVERY.md`
4. Review `docs/IMPLEMENTATION_PLAN.md`
5. Run Phase 1 through Phase 12 one at a time.

Do not use `laravel new laravel-analytics`; that creates a full application rather than a reusable package.
