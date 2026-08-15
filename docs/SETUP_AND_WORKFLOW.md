# Setup and Workflow

## Create package

```powershell
cd E:\projects
laravel package laravel-analytics
cd laravel-analytics
cursor .
```

## Recommended location for this pack

```text
laravel-analytics/
└── docs/
    └── cursor/
```

## Execution sequence

Run the bootstrap prompt, then the master prompt, then Phase 0. Review the generated implementation plan before allowing Phase 1. Continue one phase at a time.

## Quality gates after each phase

```powershell
composer validate --strict
composer test
composer lint
composer analyse
```

Use `composer verify` as the consolidated gate once it exists.
