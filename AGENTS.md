# AGENTS.md

## What this project is
This repository is the operational control center for a multi-site web portfolio and related automation work.

Primary mission (current):
1. Keep production WordPress sites healthy and professional.
2. Track remediation work in a single source of truth (SSOT).
3. Close the current quality-recovery sequence for three priority domains:
   - weareswarm.online
   - freerideinvestor.com
   - tradingrobotplug.com

## SSOT policy (must be enforced)
- `docs/MASTER_TASK_LOG.md` is the SSOT for status and execution truth.
- `docs/NEXT_UP.md` is the short execution queue and must mirror SSOT.
- Any status change must be recorded in SSOT first, then synced to NEXT_UP.
- Completed claims must include evidence (commit, deploy log, verification note, screenshot, or command output).

## Current transmission objective
Definition of done for this transmission:
- We have an accurate statement of where we are.
- We have an accurate inventory of what exists and what is complete vs pending.
- We have a precise next-action list tied to evidence.
