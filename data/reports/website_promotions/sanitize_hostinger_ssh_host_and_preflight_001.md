# Sanitize Hostinger SSH Host And Preflight 001

## Result

- Generated: 2026-06-03T17:44:56
- Status: BLOCKED_REMOTE_PREFLIGHT_FAILED
- Sanitize status: VALID_HOST_SELECTED
- Clean host count: 1
- SSH code: 255
- SSH host loaded: True
- SSH user: u996867598
- SSH port: 65002

## Remote Target

- Remote root: /home/u996867598/domains/weareswarm.site/public_html
- Remote dir: /home/u996867598/domains/weareswarm.site/public_html/dreamos-services
- Remote file: /home/u996867598/domains/weareswarm.site/public_html/dreamos-services/index.html
- Artifact: _deploy/weareswarm/dreamos-services/index.html

## Remote Preflight

- Remote status: SSH_FAILED_255
- Remote root access: UNKNOWN
- Remote homepage index: UNKNOWN
- Remote route dir: UNKNOWN
- Remote route file: UNKNOWN

## Guardrail

No upload performed. Upload only to /dreamos-services/index.html. Do not overwrite public root or homepage.

## Next Action

Fix SSH auth/host or confirm the selected Hostinger host.
