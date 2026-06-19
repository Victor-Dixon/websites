# WeAreSwarm Deploy Authority Matrix

Generated: 2026-06-13

## Decision

Do **not** patch `_deploy/` output as the source of truth. The deploy authority is split by concern:

| Concern | Authority | Evidence | Decision |
| --- | --- | --- | --- |
| Domain registry and deploy method | `ops/deployment/sites.yml` | `weareswarm.online` and `weareswarm.site` are both enabled with `deployer: unified`, SFTP deploy method, verify URLs, and remote roots. | Treat as deployment SSOT. |
| Unified deploy command | `ops/deployment/unified_deployer.py` | CLI supports `--site <domain>` and reads the deployment layer used by unified sites. | Use for publish lanes; do not invent route-specific deployers. |
| Public planner/status data | `runtime/data/weareswarm_public_contract.json`, `runtime/content/weareswarm.site/data/*.generated.json` | Runtime records identify generated public planner/status payloads and public contract metadata. | Treat runtime data as content/data SSOT when updating planner proof. |
| Route source pages | `routes/weareswarm.online/*` | Route source files exist for `dreamos-services` and `skill-tree`; `_deploy/weareswarm.online/*` mirrors deploy output. | Patch `routes/` when a matching source route exists, then rebuild/package. |
| Deploy output | `_deploy/weareswarm.online/*`, `_deploy/weareswarm/*` | Contains generated/static artifacts and manifests, including routes copied from `routes/`. | Build artifact only; not canonical source unless no source route exists and a task promotes it. |

## WeAreSwarm Domain Entries

### `weareswarm.online`

- Deployment config: `ops/deployment/sites.yml`
- Configured source path: `websites/weareswarm.online`
- Deployer: `unified`
- Verify URL: `https://weareswarm.online/.well-known/deploy.json`
- Remote root: `public_html/weareswarm.online`
- Deploy method: `sftp`
- Enabled: `true`

### `weareswarm.site`

- Deployment config: `ops/deployment/sites.yml`
- Configured source path: `websites/weareswarm.site`
- Deployer: `unified`
- Verify URL: `https://weareswarm.site/.well-known/deploy.json`
- Remote root: `public_html/weareswarm.site`
- Deploy method: `sftp`
- Enabled: `true`

## Roadmap Update Rule

Before updating a live roadmap page:

1. Identify the canonical source route or data contract for the target page.
2. Patch only the SSOT source, not `_deploy/` output.
3. Run the relevant build/package step to regenerate `_deploy/` artifacts.
4. Run repo showcase preflight before publishing.
5. Publish only through `ops/deployment/unified_deployer.py --site weareswarm.online` or a wrapper that calls that deployer.
6. Verify the live route and `.well-known/deploy.json` after deploy.

## Current Blocker Closure

This matrix closes the discovery blocker for the next lane. The official roadmap publish remains blocked until the canonical roadmap route/source is located or created under the appropriate SSOT source tree and tied to the unified deploy flow.
