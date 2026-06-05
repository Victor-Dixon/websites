# Stage xThunder allowlist and promote

Generated: 2026-06-05T01:09:53-05:00

## Problem

Previous staging attempted to add ignored audit temp files from:

`_reports/website_audit/tmp/`

## Fix

Stage only explicit xThunder production, registry, task, and report artifacts.
Do not stage ignored temp files.
Do not stage non-xThunder site payloads.
