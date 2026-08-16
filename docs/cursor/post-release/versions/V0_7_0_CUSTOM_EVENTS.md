# Cursor Prompt — v0.7.0 Custom Events, Goals and Conversions

Act as a senior Laravel package maintainer.

Read the post-release plan, current implementation, and `docs/V0_6_0_READINESS_REPORT.md` if present.

Implement **v0.7.0 only**. Do not implement v0.8+ features.

## Main objective

Add a first-party custom event system with one clear developer API such as:

```php
Analytics::event('registered');
Analytics::event('subscription_started', ['plan' => 'business']);
```

If no facade exists, expose an equally clean injectable service API. Do not create overlapping APIs.

Create portable event persistence with event name, occurred-at timestamp, optional visitor/user association, optional route/path, and bounded JSON properties.

Define and test limits for event-name length, property count, key/value length, nesting and total payload size. Do not automatically capture request bodies, headers, cookies, sessions or secrets.

Add goals/conversions based on event names. Support counts, unique converting visitors, date filtering and conversion rates only where denominator semantics are valid.

Add authorized dashboard views for event KPIs, trends, top events, event detail, goals and conversions.

Run event persistence/queries on SQLite, MySQL and PostgreSQL. Pay attention to JSON portability.

Update privacy documentation to make host applications responsible for event payload contents.

Add Pest coverage for validation, disabled behaviour, associations, payload limits, date metrics, conversions, authorization and cross-database behaviour.

Run full quality gates and create `docs/V0_7_0_READINESS_REPORT.md`.

Do not tag or publish. Stop after v0.7.0.
