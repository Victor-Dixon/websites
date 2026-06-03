# Install Hostinger SSH Key Auth 001

## Result

Installed persistent SSH key authentication for Hostinger deploy lanes.

## Verification

- SSH key auth: PASS
- SSH alias auth: PASS
- SSH alias: `hostinger-weareswarm`
- User: `u996867598`
- Port: `65002`

## Local Key

- Private key path: `/data/data/com.termux/files/home/.ssh/hostinger_u996867598_ed25519`
- Public key path: `/data/data/com.termux/files/home/.ssh/hostinger_u996867598_ed25519.pub`
- Private key committed: NO

## Usage

```bash
ssh hostinger-weareswarm
scp _deploy/weareswarm/dreamos-services/index.html hostinger-weareswarm:/home/u996867598/domains/weareswarm.site/public_html/dreamos-services/index.html
```

## Status

SSH_KEY_AUTH_READY
