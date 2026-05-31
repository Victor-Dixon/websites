# Hostinger WP Manager Harden 052

## Change

Replaced manager with hardened WP-CLI-safe implementation.

## Safety

Runtime WP-CLI commands default to:

- `--skip-plugins`
- `--skip-themes`

## Verification

MANAGER_CHECK=PASS

STATUS=PASS
