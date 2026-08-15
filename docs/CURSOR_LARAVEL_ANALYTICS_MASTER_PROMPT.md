# Cursor AI Master Prompt — `laravel-analytics`

## Role

Act as a senior Laravel package developer and open-source maintainer.

Build a production-quality, reusable, non-commercial open-source Laravel package named **`laravel-analytics`**.

This is a **Laravel package**, not a standalone Laravel application and not a portfolio-specific feature. It must be installable into other Laravel applications through Composer and designed for eventual publication on GitHub and Packagist.

The package should be suitable for public open-source maintenance: clean architecture, documented public APIs, automated tests, CI, static analysis, code style enforcement, security guidance, semantic releases, contribution documentation, and a clear MIT license.

Do not optimize the repository merely to look open source. Build a genuinely useful, maintainable package that another Laravel developer could install, understand, test, contribute to, and safely remove.

---

# 1. Technology baseline

Use the current stable versions that are compatible with each other at implementation time.

Preferred baseline:

- Laravel 13+
- PHP 8.4+
- Composer 2+
- Pest 5+
- Orchestra Testbench compatible with Laravel 13
- Laravel Pint
- Larastan / PHPStan
- Livewire 4 for the optional first-party dashboard UI
- Blade
- Tailwind-compatible markup without requiring a consumer application to adopt a package-specific frontend framework
- GitHub Actions
- Packagist-ready Composer metadata

Before installing or pinning dependencies, verify actual compatibility from the repository and current package metadata.

Do not invent version constraints.

If current dependency compatibility requires a different minimum PHP version, document the reason in `README.md`, `composer.json`, and the implementation status.

Do not add React, Vue, Inertia, Bootstrap, AdminLTE, jQuery, or other frontend frameworks.

---

# 2. Important working rules

1. **Inspect before implementing.**
2. Do not replace existing valid work without a reason.
3. Do not create a full Laravel application inside the package.
4. Prefer framework-native Laravel package mechanisms.
5. Keep the analytics engine independent from the dashboard UI where practical.
6. Use dependency injection and small focused services instead of static helper-heavy design.
7. Avoid hidden global behaviour.
8. All optional behaviour must be configurable.
9. Do not collect sensitive request data by default.
10. Do not store passwords, authorization headers, cookies, request bodies, uploaded files, session payloads, tokens, or secrets.
11. Never claim GDPR, UK GDPR, CCPA, HIPAA, CQC, or other regulatory compliance unless separately audited.
12. Every public feature must have tests.
13. Bug fixes require regression tests.
14. CI must pass before a phase is considered complete.
15. Run Pint after implementation changes.
16. Run static analysis before marking a phase complete.
17. Do not weaken tests or static-analysis rules merely to make CI green.
18. Do not commit generated vendor dependencies or secrets.
19. Keep the package useful without requiring the dashboard.
20. Prefer semantic versioning and backwards-compatible public APIs.

---

# 3. Phase 0 — Repository discovery and architecture report

**Do not implement package functionality yet.**

Inspect the complete repository.

At minimum inspect:

- `composer.json`
- `composer.lock`, if present
- `package.json`, if present
- `.gitignore`
- `.gitattributes`
- `.editorconfig`
- `.github/`
- `src/`
- `config/`
- `database/`
- `resources/`
- `routes/`
- `tests/`
- `workbench/`, if present
- `phpunit.xml` / `phpunit.xml.dist`
- `phpstan.neon*`
- `pint.json`
- `README.md`
- `LICENSE`
- `CHANGELOG.md`
- `CONTRIBUTING.md`
- `SECURITY.md`
- any existing implementation plan or specification

Determine:

1. Whether the repository is blank, an existing Laravel package, or accidentally a full Laravel application.
2. Current PHP constraint.
3. Current Laravel / Illuminate constraints.
4. Current Pest version and PHP compatibility.
5. Current Orchestra Testbench version.
6. Current Livewire compatibility.
7. Existing package namespace.
8. Existing Composer vendor/package name.
9. Existing GitHub Actions.
10. Existing documentation and licenses.
11. Anything that conflicts with this specification.
12. Security or privacy risks in the proposed analytics design.

If the repository is blank or not yet a proper Laravel package, use the **official Laravel package skeleton / package tooling** as the preferred foundation rather than inventing a custom test harness.

Create or update:

`docs/IMPLEMENTATION_PLAN.md`

The plan must contain:

- current state
- target architecture
- dependency decisions
- phases
- risks
- acceptance criteria
- unresolved decisions
- migration / compatibility notes

Stop after Phase 0 and report the findings before implementing later phases.

---

# 4. Product definition

`laravel-analytics` provides first-party, self-hosted application analytics for Laravel applications.

The v1 package should support:

### Traffic analytics

- page views
- unique visitors
- visits / sessions where safely derivable
- route name
- request path
- HTTP method
- referrer host / URL according to configuration
- user-agent metadata according to configuration
- authenticated user ID only when explicitly enabled
- response status
- request duration
- first seen / last seen timestamps
- daily aggregate reporting

### Visitor analytics

- unique visitor counting
- configurable visitor identification strategy
- privacy-aware hashed visitor identifiers
- optional IP storage
- configurable IP anonymisation / hashing
- IPv4 and IPv6 support
- configurable ignored routes
- configurable ignored HTTP methods
- configurable ignored user agents / bots when implemented reliably
- configurable retention

### Error analytics

Capture **HTTP application errors** in v1 without changing normal Laravel exception behaviour.

Store only safe diagnostic metadata by default:

- exception class
- safe message summary
- route / path
- HTTP method
- status code where available
- file
- line
- first occurrence
- latest occurrence
- occurrence count
- fingerprint

Do **not** persist:

- request bodies
- passwords
- cookies
- sessions
- bearer tokens
- authorization headers
- API keys
- uploaded file contents
- environment variables
- full application configuration
- arbitrary request headers

The error tracker must rethrow / preserve Laravel's normal exception handling after recording.

Make error tracking disableable.

Document clearly that v1 error tracking covers the package's supported HTTP path and is not a replacement for full APM, queue monitoring, or centralized logging.

### IP access controls

Provide optional IP banning:

- exact IPv4 addresses
- exact IPv6 addresses
- enabled / disabled state
- reason
- timestamps
- optional expiry
- ban
- unban
- automatic expiry handling
- middleware that blocks matching active bans
- configurable blocked response
- audit-safe metadata

Do not implement broad CIDR/range matching unless it is well-tested and intentionally included in scope.

The banning feature must be **disabled by default** so installing an analytics package cannot unexpectedly change access control.

### Dashboard

Provide an optional Livewire 4 dashboard with:

- overview KPI cards
- unique visitors
- page views
- visits
- error count
- banned IP count
- traffic trends
- top pages
- top referrers
- response status breakdown
- recent errors
- error detail
- IP ban list
- ban IP action
- unban IP action
- filtering by date range
- pagination
- empty states
- loading states
- accessible controls

The UI should have an AdminLTE-inspired information density only if useful, but **must not depend on AdminLTE**.

Use Blade + Livewire and Tailwind-compatible utility markup.

Do not ship a second application-wide layout that fights with consuming applications.

Provide a package-owned dashboard layout or documented integration points that are isolated and publishable.

---

# 5. Package architecture

Use a structure close to the following, adapting it only when the official Laravel package skeleton or the inspected repository provides a better convention:

```text
laravel-analytics/
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.yml
│   │   ├── feature_request.yml
│   │   └── config.yml
│   ├── workflows/
│   │   ├── tests.yml
│   │   ├── static-analysis.yml
│   │   ├── code-style.yml
│   │   └── release.yml
│   ├── dependabot.yml
│   └── pull_request_template.md
│
├── config/
│   └── analytics.php
│
├── database/
│   ├── factories/
│   └── migrations/
│
├── docs/
│   ├── IMPLEMENTATION_PLAN.md
│   ├── INSTALLATION.md
│   ├── CONFIGURATION.md
│   ├── PRIVACY.md
│   ├── ARCHITECTURE.md
│   ├── DASHBOARD.md
│   └── RELEASES.md
│
├── resources/
│   └── views/
│       ├── components/
│       ├── livewire/
│       └── layouts/
│
├── routes/
│   └── web.php
│
├── src/
│   ├── AnalyticsServiceProvider.php
│   ├── Contracts/
│   ├── Console/
│   │   └── Commands/
│   ├── Data/
│   ├── Enums/
│   ├── Events/
│   ├── Exceptions/
│   ├── Http/
│   │   └── Middleware/
│   ├── Livewire/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   └── Support/
│
├── tests/
│   ├── Feature/
│   ├── Unit/
│   ├── Pest.php
│   └── TestCase.php
│
├── workbench/
│
├── .editorconfig
├── .gitattributes
├── .gitignore
├── CHANGELOG.md
├── CODE_OF_CONDUCT.md
├── CONTRIBUTING.md
├── LICENSE
├── README.md
├── SECURITY.md
├── composer.json
├── phpstan.neon.dist
├── phpunit.xml.dist
└── pint.json
```

Do not create empty architectural directories merely to match the diagram. Only create directories that have a purpose.

---

# 6. Composer package design

The Composer package must be Packagist-ready.

Use:

```text
type: library
```

The final package name must be:

```text
<packagist-vendor>/laravel-analytics
```

Determine `<packagist-vendor>` from existing repository ownership/configuration when reliable. Otherwise leave a clearly documented placeholder and report that the maintainer must choose it before publication.

`composer.json` must include appropriate:

- `name`
- `description`
- `type`
- `license`
- `keywords`
- `homepage`, if known
- `support`
- `authors`
- PHP requirement
- Laravel / Illuminate requirements
- Livewire requirement only if the UI is shipped as part of the core package
- development dependencies
- PSR-4 autoloading
- test PSR-4 autoloading
- Laravel package discovery
- Composer scripts for test, lint, static analysis, and full verification

Suggested scripts, adapted to actual tooling:

```text
composer test
composer lint
composer analyse
composer test:coverage
composer verify
```

`composer verify` should run all mandatory quality gates suitable for local development and CI.

Do not add dependencies simply for convenience when Laravel provides the capability natively.

---

# 7. Laravel service provider

Create a focused `AnalyticsServiceProvider`.

It should use Laravel package conventions for:

- configuration merging
- publishable config
- migrations
- routes
- views
- Livewire component registration where necessary
- Artisan command registration
- container bindings
- package discovery
- `about` command information where appropriate

Publishing tags should be predictable, for example:

```text
analytics-config
analytics-migrations
analytics-views
```

Do not require users to manually edit `bootstrap/providers.php` when Composer package discovery can register the provider.

---

# 8. Configuration

Create:

`config/analytics.php`

Configuration should include sensible, privacy-conscious defaults for:

- `enabled`
- dashboard enabled
- dashboard path / route prefix
- dashboard route name prefix
- dashboard middleware
- authorization mechanism
- traffic tracking
- error tracking
- IP banning
- trusted proxies considerations
- whether raw IP addresses are stored
- IP hashing / anonymisation
- hashing salt source
- authenticated user tracking
- referrer collection
- user-agent collection
- ignored paths
- ignored route names
- ignored HTTP methods
- retention period
- pruning
- excluded response statuses if needed
- sampling, only if implemented
- dashboard pagination
- cache TTL for aggregate widgets

Do not put closures into publishable configuration.

Use environment variables sparingly. Application configuration should remain the primary public interface.

---

# 9. Database model

Design migrations carefully for production-scale querying.

Use package-specific table names such as:

```text
analytics_page_views
analytics_visitors
analytics_errors
analytics_ip_bans
```

Only introduce an additional visits/sessions table if the domain model actually requires it.

Indexes should support common queries such as:

- date range
- visitor hash
- route/path
- status code
- error fingerprint
- active IP ban lookup
- expiry lookup

Avoid storing redundant high-cardinality data unnecessarily.

Do not use database-vendor-specific SQL unless isolated and tested.

The package should work with Laravel-supported relational databases where practical, with SQLite used for fast package tests.

Models should not be needlessly coupled to a host application's `User` model.

If user association is supported, make the model / foreign key behaviour configurable and optional.

---

# 10. Analytics tracking pipeline

Implement traffic tracking through focused middleware and services.

A typical request flow may be:

```text
Request
  -> ignored/excluded check
  -> ban middleware if explicitly enabled
  -> application request
  -> analytics response capture
  -> safe visitor identifier
  -> page-view persistence
  -> aggregate/cache update where appropriate
  -> response
```

Do not noticeably delay responses with unnecessary synchronous work.

If queue support is added, it must remain optional and the synchronous default must still be correct.

Make the tracking classes independently testable.

Avoid tracking the analytics dashboard itself by default.

Avoid infinite loops when tracking errors produced by analytics routes or storage.

---

# 11. Unique visitor strategy

Do not treat a raw IP address alone as a permanent identity.

Create a documented visitor identification strategy.

Default behaviour should use a one-way identifier based only on intentionally selected request metadata and an application-specific secret/salt.

The implementation must:

- avoid reversible fingerprints
- avoid cookies unless intentionally added and documented
- support IPv4 and IPv6
- avoid pretending the visitor count is mathematically perfect
- document limitations of NAT, changing IPs, VPNs, shared devices, proxies, bots, and user-agent changes

Provide a contract so the visitor identification strategy can later be replaced.

---

# 12. Error tracking

Implement error tracking conservatively.

Preferred architecture:

- middleware or another package-safe Laravel integration point catches a `Throwable`
- safe metadata is passed to an error recorder
- a stable fingerprint groups repeated errors
- occurrence count and timestamps are updated
- the original exception is rethrown
- Laravel's normal exception reporting/rendering remains intact

The error recorder itself must not cause recursive failures.

If analytics persistence fails while handling an application exception, fail safely and preserve the original application exception.

Provide tests for this behaviour.

---

# 13. IP banning

Implement:

- `IpBan` model
- ban service
- unban service
- active/expired logic
- middleware
- Livewire management screen
- authorization
- validation
- tests

The middleware must:

- be opt-in
- normalize addresses safely
- reject invalid input
- support IPv4 and IPv6
- avoid trusting spoofable forwarding headers unless Laravel's trusted-proxy configuration has resolved the client IP
- return a configurable 403-style response by default
- never lock administrators out merely because the package was installed

Provide a CLI recovery mechanism such as:

```text
php artisan analytics:ip-unban <ip>
```

so a ban can be reversed without accessing the dashboard.

---

# 14. Retention and pruning

Analytics data grows indefinitely unless managed.

Create a command such as:

```text
php artisan analytics:prune
```

It should prune records according to configured retention rules.

Make it safe to run repeatedly.

Document how a host application may schedule it.

Do not silently register an aggressive destructive schedule.

Add tests for:

- retention cutoff
- idempotency
- expired ban handling where relevant
- keeping records inside retention

---

# 15. Dashboard authorization

Never expose the dashboard publicly by default.

Provide a package-owned authorization mechanism using Laravel-native authorization patterns.

Requirements:

- safe deny-by-default behaviour outside clearly permitted local/testing contexts
- configurable middleware
- documented integration with application authentication
- no dependency on a specific User model
- tests proving unauthorized users cannot access protected analytics routes

Do not hard-code an `admin` boolean column.

---

# 16. Livewire dashboard

Build the dashboard incrementally.

Suggested components:

```text
AnalyticsDashboard
TrafficOverview
TrafficChart
TopPages
TopReferrers
StatusBreakdown
RecentErrors
ErrorDetails
IpBanManager
```

Use fewer components if that creates a cleaner implementation.

Requirements:

- Livewire 4 conventions
- server-side pagination
- validated filters
- validated IP ban input
- authorization on mutating actions
- no N+1 query patterns
- clear empty states
- query-string date filters only when useful
- accessible buttons/forms
- destructive actions clearly distinguished
- no full-page JavaScript SPA requirement

Do not create application-specific sidebar navigation. Instead document how consuming applications can link to the package dashboard.

---

# 17. Public API and extension points

Create contracts only where they provide real value.

Potential extension points:

```text
VisitorIdentifier
AnalyticsRecorder
ErrorRecorder
IpBanRepository
AnalyticsQuery
```

Avoid abstraction for abstraction's sake.

Document the stable public API.

Classes that are internal implementation details should not be advertised as extension points.

---

# 18. Artisan commands

Implement only useful commands.

Recommended:

```text
php artisan analytics:install
php artisan analytics:prune
php artisan analytics:ip-ban <ip>
php artisan analytics:ip-unban <ip>
```

`analytics:install` should guide installation safely and should not overwrite published files without explicit handling.

If migrations auto-load and publishing is optional, make that clear.

Commands require feature tests.

---

# 19. Pest test suite

Use Pest and Orchestra Testbench for package testing.

Tests must cover behaviour, not just line coverage.

At minimum include tests for:

### Package boot

- service provider loads
- package discovery works
- config defaults load
- publishable resources are registered
- routes are registered only when enabled

### Tracking

- valid page view is recorded
- ignored route is not recorded
- ignored path is not recorded
- ignored method is not recorded
- analytics dashboard does not self-track by default
- response status is captured
- duration is sane
- disabled analytics records nothing

### Visitors

- repeat visitor behaviour
- unique visitor behaviour
- visitor hash does not expose raw inputs
- raw IP is omitted when configured
- IPv4 support
- IPv6 support

### Errors

- supported HTTP exception is recorded
- exception class is stored
- fingerprint groups matching errors
- occurrence count increments
- original exception behaviour is preserved
- sensitive request information is not persisted
- failure in analytics recording does not replace the original exception

### IP bans

- valid IPv4 can be banned
- valid IPv6 can be banned
- invalid IP is rejected
- active ban blocks request
- disabled ban middleware does not block
- expired ban does not block
- ban can be removed
- unauthorized dashboard user cannot ban/unban
- CLI unban recovery works

### Retention

- old analytics are pruned
- recent analytics remain
- command is idempotent

### Dashboard

- authorized user can access
- unauthorized user is denied
- expected KPI values render
- date filters work
- top pages query works
- errors paginate
- IP bans paginate
- actions validate input

### Database

- migrations run on SQLite
- indexes / constraints required by the domain are present where testable
- model casts behave correctly

Use factories or test helpers where they improve clarity.

No meaningless tests such as `expect(true)->toBeTrue()`.

---

# 20. Compatibility test matrix

Use GitHub Actions matrix testing.

At minimum, test the actual supported PHP + Laravel combinations declared by Composer.

Do not claim compatibility that CI does not test.

If the package initially supports only:

```text
PHP 8.4+
Laravel 13+
```

then test the relevant supported PHP versions available in GitHub Actions.

When adding additional Laravel major versions later, expand the matrix intentionally.

Use `composer update` / dependency constraint strategies suitable for package compatibility testing rather than relying on a single committed lockfile.

---

# 21. GitHub Actions

Create production-quality workflows.

## `.github/workflows/tests.yml`

Run on:

- pull requests
- pushes to the default development branch
- pushes to `main`

Run:

- dependency installation
- package tests
- compatibility matrix

Use Composer caching appropriately.

## `.github/workflows/static-analysis.yml`

Run Larastan / PHPStan at the configured level.

Start strict enough to provide value, then improve intentionally.

Do not hide real errors behind broad ignore rules.

## `.github/workflows/code-style.yml`

Run:

```text
vendor/bin/pint --test
```

## `.github/workflows/release.yml`

Do not automatically publish arbitrary commits as releases.

Use a safe tag/release workflow.

Validate tests and quality gates before release steps.

Packagist publication should rely on supported Packagist/GitHub integration or documented API-token workflow rather than embedding credentials.

Use repository secrets for any credentials.

Never commit tokens.

---

# 22. Dependabot

Create:

`.github/dependabot.yml`

Configure update checks for:

- Composer
- GitHub Actions

Use a reasonable cadence such as weekly.

Avoid excessive automated PR noise.

---

# 23. Open-source repository files

Create professional versions of all of the following.

## `README.md`

README must include:

1. package name
2. concise purpose
3. project status
4. CI / quality badges using real repository URLs only when known
5. requirements
6. installation
7. publishing configuration
8. migration/install steps
9. enabling tracking
10. dashboard access
11. dashboard authorization
12. IP banning
13. error tracking
14. privacy defaults
15. data retention
16. Artisan commands
17. configuration overview
18. testing
19. static analysis
20. contributing
21. security reporting
22. versioning
23. license

The expected installation experience should eventually be close to:

```bash
composer require <vendor>/laravel-analytics
```

Do not include fake Packagist badges or fake download counts before the package exists.

## `LICENSE`

Use the standard MIT License.

Use the current year and actual copyright holder once known.

Do not invent the copyright holder.

## `CONTRIBUTING.md`

Include:

- development prerequisites
- fork/clone workflow
- branch guidance
- Composer install
- tests
- Pint
- static analysis
- documentation expectations
- requirement for tests with behaviour changes
- pull-request expectations
- issue guidance
- backwards-compatibility expectations
- respectful review process

## `SECURITY.md`

Include:

- supported release policy
- private vulnerability reporting process
- what information reporters should include
- expected responsible disclosure behaviour

Do not publish a fake security email.

If no security contact has been supplied, use GitHub private vulnerability reporting as the preferred documented route where available and leave any email as an explicit maintainer TODO.

## `CODE_OF_CONDUCT.md`

Use a recognised, appropriate open-source code of conduct, preserving attribution and terms.

## `CHANGELOG.md`

Use a clear release-oriented changelog format.

Do not fabricate historical releases.

Start with an Unreleased section until the first actual release.

---

# 24. GitHub issue forms and PR template

Create structured issue forms.

### Bug report

Ask for:

- package version
- Laravel version
- PHP version
- database
- reproduction steps
- expected behaviour
- actual behaviour
- minimal reproduction if possible
- logs with explicit warning to redact secrets

### Feature request

Ask for:

- problem
- proposed behaviour
- alternatives
- backwards-compatibility considerations

### Pull request template

Include:

- summary
- motivation
- implementation
- tests
- backwards compatibility
- documentation
- checklist

Do not create bureaucratic templates that discourage legitimate contributors.

---

# 25. Privacy documentation

Create:

`docs/PRIVACY.md`

Explain exactly what the package records by default and what optional settings may increase collection.

Include:

- raw IP policy
- hashed identifier strategy
- referrer collection
- user-agent collection
- authenticated user association
- error metadata
- retention
- pruning
- backup implications
- dashboard access
- IP-ban data

State clearly:

> This package provides technical privacy controls but does not itself make an application compliant with any specific privacy law or regulatory framework.

---

# 26. Architecture documentation

Create:

`docs/ARCHITECTURE.md`

Describe:

- package boot lifecycle
- request tracking pipeline
- visitor identification
- data model
- error recording
- ban enforcement
- dashboard query path
- pruning
- extension contracts
- major security boundaries

Include a simple Mermaid diagram if useful and supported by GitHub Markdown.

---

# 27. Packagist readiness

Prepare the repository for Packagist.

Before publication verify:

- public GitHub repository exists
- final Composer package name is available
- `composer.json` validates
- license is valid
- source URL is correct
- autoloading works
- package installs cleanly into a fresh Laravel 13 application/workbench
- tags follow semantic versioning
- README installation command matches the real package name
- repository is not marked private
- no secrets are committed

Create:

`docs/RELEASES.md`

Document maintainer steps:

```text
1. Ensure main is green.
2. Review CHANGELOG.
3. Choose semantic version.
4. Create signed/normal Git tag according to project policy.
5. Push tag.
6. Create GitHub Release.
7. Verify Packagist sees the release.
8. Test composer require in a fresh Laravel application.
```

Do not attempt to publish to Packagist unless valid maintainer credentials and explicit instruction are available.

---

# 28. GitHub repository settings documentation

Add a section to the maintainer docs recommending:

- public repository
- Issues enabled
- Discussions optional
- private vulnerability reporting enabled
- branch protection / ruleset on `main`
- required status checks
- pull requests before merge
- deletion of stale head branches where appropriate
- Dependabot alerts
- secret scanning where available

Do not assume repository-level settings can be changed from code.

---

# 29. Semantic versioning

Use Semantic Versioning.

Before `1.0.0`, document that APIs may evolve.

Target milestones:

```text
0.1.0 — package foundation + page views
0.2.0 — visitor analytics
0.3.0 — error analytics
0.4.0 — IP banning
0.5.0 — Livewire dashboard
0.6.0 — privacy/retention hardening
0.9.0 — release candidate quality
1.0.0 — stable documented API
```

These are planning milestones only.

Do not create fake releases or tags merely to satisfy this document.

---

# 30. Implementation phases

Implement only one phase at a time unless explicitly instructed otherwise.

## Phase 0 — Discovery

Repository audit and `docs/IMPLEMENTATION_PLAN.md`.

**Do not implement functionality.**

## Phase 1 — Package foundation

Create/normalize official package skeleton, Composer metadata, namespaces, service provider, workbench and baseline tests.

Acceptance criteria:

- package boots
- Composer validation passes
- tests pass
- Pint passes
- static-analysis baseline passes

## Phase 2 — Configuration and persistence

Implement config, migrations, models, factories/test helpers, indexes and publishing.

Acceptance criteria:

- migrations run
- package config loads and publishes
- data model is documented
- database tests pass

## Phase 3 — Traffic analytics

Implement request/page-view tracking and safe exclusions.

Acceptance criteria:

- enabled requests track
- disabled/excluded requests do not
- dashboard routes are excluded by default
- tests pass

## Phase 4 — Visitor analytics

Implement visitor identification and privacy controls.

Acceptance criteria:

- unique/repeat visitor behaviour tested
- IP handling tested
- privacy defaults documented

## Phase 5 — Error analytics

Implement safe HTTP error recording and fingerprint aggregation.

Acceptance criteria:

- errors recorded
- sensitive data excluded
- original exception behaviour preserved
- analytics recorder failure cannot replace original error

## Phase 6 — IP banning

Implement bans, expiry, middleware, services and CLI recovery.

Acceptance criteria:

- IPv4/IPv6 tested
- opt-in behaviour tested
- expired bans tested
- CLI recovery tested

## Phase 7 — Retention and maintenance

Implement pruning and maintenance commands.

Acceptance criteria:

- retention works
- repeated execution is safe
- docs explain scheduling

## Phase 8 — Livewire dashboard

Implement protected dashboard and management UI.

Acceptance criteria:

- authorization enforced
- analytics metrics render correctly
- filtering/pagination work
- ban/unban requires authorization and validation
- Livewire tests pass

## Phase 9 — OSS documentation

Finish README, CONTRIBUTING, SECURITY, CODE_OF_CONDUCT, privacy docs, architecture docs and changelog.

Acceptance criteria:

- no fake metadata
- installation can be followed by another developer
- privacy behaviour is explicit
- contribution process is complete

## Phase 10 — CI and repository automation

Implement GitHub Actions and Dependabot.

Acceptance criteria:

- tests matrix green
- Pint green
- static analysis green
- dependency update configuration valid

## Phase 11 — Packagist/release readiness

Validate installability, Composer metadata, release docs, package name and fresh Laravel installation.

Acceptance criteria:

- `composer validate` passes
- fresh install test passes
- no secrets
- no placeholder package name except explicitly reported blockers
- README command is accurate once vendor is known

## Phase 12 — 1.0 hardening

Perform security, privacy, performance, query, API and backwards-compatibility review.

Do not call the package `1.0.0` automatically.

Produce a readiness report and wait for maintainer release decision.

---

# 31. Quality gates

Before completing every implementation phase, run the relevant commands.

Expected final gates include equivalents of:

```bash
composer validate --strict
composer install
composer test
composer lint
composer analyse
composer verify
```

If coverage tooling is configured:

```bash
composer test:coverage
```

Coverage percentage is a diagnostic, not a substitute for behavioural quality.

Report:

- commands run
- pass/fail result
- tests executed
- important warnings
- files changed
- remaining risks

---

# 32. Definition of done

The project is ready for public OSS release only when:

- [ ] repository is a real Laravel package rather than an application
- [ ] Laravel 13 compatibility is verified
- [ ] supported PHP versions are verified in CI
- [ ] Composer package name is final
- [ ] package is MIT licensed
- [ ] automatic Laravel package discovery works
- [ ] configuration is publishable
- [ ] migrations work
- [ ] analytics tracking works
- [ ] visitor handling is privacy-conscious
- [ ] error tracking does not expose sensitive request data
- [ ] IP banning is opt-in
- [ ] dashboard is protected
- [ ] pruning exists
- [ ] Pest suite passes
- [ ] static analysis passes
- [ ] Pint passes
- [ ] GitHub Actions pass
- [ ] Dependabot is configured
- [ ] README is complete
- [ ] CONTRIBUTING is complete
- [ ] SECURITY is complete
- [ ] privacy documentation is complete
- [ ] CHANGELOG is honest
- [ ] package installs into a clean Laravel 13 environment
- [ ] no secrets or `.env` files are committed
- [ ] Packagist instructions are accurate
- [ ] public APIs are documented
- [ ] no fake releases, stars, badges, download counts, contributors, or claims exist

---

# 33. First instruction to execute now

Read this specification completely.

Then perform **Phase 0 only**.

Do not implement package functionality yet.

Inspect the repository and create/update:

`docs/IMPLEMENTATION_PLAN.md`

The Phase 0 report must tell me:

1. what already exists;
2. what must be installed;
3. what must be changed;
4. what must be created;
5. current dependency/version compatibility;
6. proposed Composer package name and namespace;
7. architecture decisions;
8. security/privacy risks;
9. conflicts with this specification;
10. the exact files expected to change in Phase 1;
11. Phase 1 acceptance criteria;
12. any blockers that genuinely cannot be resolved from the repository.

Do not ask questions that can be answered by inspecting the repository.

Do not begin Phase 1 until I explicitly request it.
