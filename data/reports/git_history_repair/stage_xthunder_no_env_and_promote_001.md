# Stage xThunder without ignored env

Generated: 2026-06-05T01:10:34-05:00

## Decision

Excluded `runtime/env/hostinger/sites/xthunder.site.env`.

Reason: `runtime/env` is ignored and may contain private deploy material. Do not force-add secrets or local environment files.

## Staging policy

Allowed:

- xThunder production site files
- deploy registry
- public config registry
- xThunder tasks
- xThunder reports
- human-readable audit report

Blocked:

- `.git/`
- `_reports/website_audit/tmp/`
- `runtime/env/`
- non-xThunder site payloads
