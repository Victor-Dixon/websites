# Diagnose Hostinger SSH Auth State 001

## Context

- Target: weareswarm.site route-only deploy
- Remote root: /home/u996867598/domains/weareswarm.site/public_html
- Upload: NOT ATTEMPTED

## SSH Client

/data/data/com.termux/files/usr/bin/ssh
OpenSSH_10.3p1, OpenSSL 3.6.2 7 Apr 2026

## SSH Agent

- SSH_AUTH_SOCK: MISSING
Could not open a connection to your authentication agent.

## SSH Directory Inventory

authorized_keys 600 0 bytes
config 600 167 bytes
config.bak.20260512_192140 600 146 bytes
dreamos_desktop_ed25519 600 419 bytes
dreamos_desktop_ed25519.pub 644 107 bytes
dreamsync_mobile_ed25519 600 411 bytes
dreamsync_mobile_ed25519.pub 600 99 bytes
hostinger_freeride_deploy_key 600 432 bytes
hostinger_freeride_deploy_key.pub 644 115 bytes
id_rsa 600 3389 bytes
id_rsa.pub 600 752 bytes
known_hosts 600 2183 bytes
known_hosts.old 600 1235 bytes
weareswarm_ed25519 600 411 bytes
weareswarm_ed25519.pub 600 100 bytes

## SSH Config Hostinger Entries


## Relevant Env Redacted

HOSTINGER_SSH_PORT=65002
HOSTINGER_SSH_USER=u996867598

## Status

AUTH_DIAGNOSIS_READY
