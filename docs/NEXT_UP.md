# NEXT UP (Execution Queue, mirrored from SSOT)

**Last Updated:** 2026-03-24 (UTC, mirrored after privileged-attempt refresh)
**SSOT Source:** `docs/MASTER_TASK_LOG.md`

## What this project is
This repo is the operations hub for a multi-site website portfolio. Right now, the active mission is to finish quality-recovery execution and verification for:
- weareswarm.online
- freerideinvestor.com
- tradingrobotplug.com

## Where we are now
- We are in **Phase 3 of 4**: Production Deployment + Cache Clear.
- Code remediation is complete and production sync is logged.
- 2026-03-24 smoke checks and screenshot evidence were added for all three domains.
- A 2026-03-24 privileged purge follow-up attempt (with available token material) was executed; purge remains blocked, but smoke + external screenshot URL evidence was refreshed.
- Remaining blocker is **privileged** cache purge confirmation.

## Definitive Inventory Snapshot
| Domain | Code Fix | Production Sync | Privileged Cache Purge | Smoke Check (2026-03-24) | Visual QA Proof | Current State |
|---|---:|---:|---:|---:|---:|---|
| weareswarm.online | ✅ | ✅ | ❌ (blocked: auth required) | ✅ (refreshed) | ✅ (external URL proof refreshed) | Awaiting privileged purge |
| freerideinvestor.com | ✅ | ✅ | ❌ (blocked: auth required) | ✅ (refreshed) | ✅ (external URL proof refreshed) | Awaiting privileged purge |
| tradingrobotplug.com | ✅ | ✅ | ❌ (blocked: auth required) | ✅ (refreshed) | ✅ (external URL proof refreshed) | Awaiting privileged purge |

Evidence references:
- `docs/deployment/PHASE3_EXECUTION_2026-03-21.md`
- `docs/evidence/phase3/2026-03-24/PHASE3_CACHE_PURGE_AND_SMOKE_2026-03-24.md`
- `docs/evidence/phase3/2026-03-24/SCREENSHOT_URLS_2026-03-24.md`
- `docs/evidence/phase3/2026-03-24/2026-03-24T013347Z_privileged_attempt/PHASE3_PRIVILEGED_PURGE_ATTEMPT_AND_SMOKE.md`

## Immediate Next Actions (in order)
1. Obtain privileged operator access (WP admin/CDN/API token) for all three domains.
2. Execute authenticated cache purge and capture timestamped evidence for each domain.
3. Re-run smoke checks immediately after confirmed purge and add updated evidence artifacts.
4. Update SSOT (`docs/MASTER_TASK_LOG.md`) first, then mirror here, and mark Phase 3 complete if purge is confirmed across all three domains.
5. Begin Phase 4 visual QA/sign-off.

## Block (current)
- **Reason:** No authenticated WP/plugin session is available; LiteSpeed endpoint still returns 400 even with Bearer token injection, and available Hostinger API schema does not expose purge/cache endpoints.
- **Exact unblock action:** Provide WP admin credentials, a purge-capable CDN/API token with endpoint details, or operator-provided purge logs with timestamps for ingestion.

## Copy/Paste Prompt You Can Send to Codex Agent (Next Run)
```text
You are working in /workspace/Websites.

Follow SSOT policy strictly:
- Read docs/MASTER_TASK_LOG.md first.
- Update docs/MASTER_TASK_LOG.md first.
- Mirror to docs/NEXT_UP.md second.

Execute the highest-priority incomplete Phase 3 action for:
- weareswarm.online
- freerideinvestor.com
- tradingrobotplug.com

This run should focus on:
1) authenticated privileged cache purge execution,
2) immediate post-purge smoke checks,
3) screenshot evidence attachment,
4) SSOT inventory/status update.

If blocked, record block reason + exact unblock action.
Then commit and open PR.

End your response with:
- Current state snapshot
- Evidence list
- Single next action
- "Prompt for next agent" (a ready-to-send prompt that asks the next agent to generate yet another next-agent prompt at the end)
```

## Definition of Done for this transmission
- Accurate statement of where we are.
- Accurate statement of what we have (with evidence links).
- Accurate statement of what we do next (ordered and executable).
