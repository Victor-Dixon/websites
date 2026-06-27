# Websites SSOT

Canonical website and deployment hub for Victor Dixon's public web surfaces, client demos, and Dream.OS product experiments.

This repository consolidates website source, static deploy packets, deployment tooling, verification scripts, and domain-specific build surfaces into one version-controlled source of truth.

## What This Proves

- Multi-site website consolidation
- Static and framework-based deploy workflows
- Hostinger/VPS deployment packet generation
- Domain-specific product surfaces
- Verification scripts before deployment
- Reusable website tooling for client and internal projects

## Active Surfaces

| Surface | Purpose |
|---|---|
| `weareswarm.online` | Dream.OS public operator/status surface |
| `weareswarm.site` | Public showcase surface |
| `maskzero.site` | Portfolio / interactive demo surface |
| `freerideinvestor.com` | Trading discipline and content surface |
| `ariajet.site` | Static takeover / brand surface |
| `xthunder.site` | Theme scaffold / web experiment |

## Repository Role

This repository is the canonical working source for public web deployment assets.

It replaces scattered standalone website repos with a single structure where:

- source is versioned
- deploy packets are generated intentionally
- domain ownership is explicit
- promotion artifacts can be reviewed before deployment
- verification scripts prove what changed

## Common Commands

```bash
npm install
npm run lint
npm run typecheck
npm run build
```

Domain-specific verification may use scripts under `tools/`.

```bash
npm run audit:maskzero
npm run verify:digital-dreamscape-graphics
```

## Deployment Discipline

Before deploying:

1. Identify the target domain.
2. Generate or inspect the deployment packet.
3. Run the relevant verification command.
4. Confirm changed files with `git diff --stat`.
5. Promote only reviewed assets.

## Current Status

Public web consolidation is active. This repository is suitable as a professional proof surface for:

- website cleanup
- deployment automation
- repo consolidation
- client-facing static/web builds
- verification-driven delivery