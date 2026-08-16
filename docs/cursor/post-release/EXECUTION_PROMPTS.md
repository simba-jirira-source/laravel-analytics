# Cursor Execution Prompts

Assumes this pack is stored under `docs/cursor/post-release/`.

## v0.6.0

```text
Read @docs/cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md completely.
Then read @docs/cursor/post-release/versions/V0_6_0_FOUNDATION.md.

Execute v0.6.0 only.
Preserve working v0.5.0 behaviour unless the prompt explicitly calls for a documented pre-1.0 breaking cleanup.
Run all quality and database compatibility gates.
Do not begin v0.7.0.
Do not tag or publish.
Stop and report the results.
```

## v0.7.0

```text
Read @docs/cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md completely.
Read @docs/V0_6_0_READINESS_REPORT.md if present.
Then read @docs/cursor/post-release/versions/V0_7_0_CUSTOM_EVENTS.md.

Execute v0.7.0 only.
Run all quality/database gates.
Do not begin v0.8.0.
Do not tag or publish.
Stop and report the results.
```

## v0.8.0

```text
Read @docs/cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md completely.
Read @docs/V0_7_0_READINESS_REPORT.md if present.
Then read @docs/cursor/post-release/versions/V0_8_0_ANALYTICS_DEPTH.md.

Execute v0.8.0 only.
Do not begin v0.9.0.
Do not tag or publish.
Stop and report the results.
```

## v0.9.0

```text
Read @docs/cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md completely.
Read @docs/V0_8_0_READINESS_REPORT.md if present.
Then read @docs/cursor/post-release/versions/V0_9_0_RELEASE_CANDIDATE.md.

Execute v0.9.0 only.
Treat this as release-candidate stabilization; avoid unrelated major features.
Do not begin 1.0.
Do not tag or publish.
Stop and report the results.
```

## v1.0.0

```text
Read @docs/cursor/post-release/POST_RELEASE_IMPROVEMENT_PLAN.md completely.
Read @docs/V0_9_0_RELEASE_CANDIDATE_REPORT.md and @docs/PUBLIC_API.md if present.
Then read @docs/cursor/post-release/versions/V1_0_0_STABLE_RELEASE.md.

Perform 1.0 final hardening only.
Do not create a v1.0.0 tag, GitHub Release, or Packagist publication.
Create docs/V1_FINAL_RELEASE_REPORT.md and stop for maintainer approval.
```
