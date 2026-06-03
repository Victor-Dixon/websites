# Preflight WeAreSwarm Site SFTP Route Upload 001

- Generated: 2026-06-03T17:39:50
- Status: BLOCKED_MISSING_SSH_HOST
- Selected domain: weareswarm.site
- SSH host present: False
- SSH user: u996867598
- SSH port: 65002
- Remote root: /home/u996867598/domains/weareswarm.site/public_html
- Remote dir: /home/u996867598/domains/weareswarm.site/public_html/dreamos-services
- Remote file: /home/u996867598/domains/weareswarm.site/public_html/dreamos-services/index.html
- Artifact: _deploy/weareswarm/dreamos-services/index.html

## Guardrail

No upload performed. Upload only to /dreamos-services/index.html. Do not overwrite public root or homepage.

## Remote Preflight

- Remote status: SKIPPED
- Remote root access: UNKNOWN
- Remote homepage index: UNKNOWN
- Remote route dir: UNKNOWN

## Block

Set HOSTINGER_SSH_HOST, HOSTINGER_SSH_USER, and HOSTINGER_SSH_PORT, then rerun preflight.

## Repair Note

Repaired markdown report generation after shell interpolation noise from prior preflight lane.
