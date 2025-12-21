# Auto-deployment (optional)

## Overview

This repository includes an optional workflow to deploy changed theme files directly to hosting based on the staged git diff. It is implemented by `tools/auto_deploy_hook.py`.

Important notes:

- **Not enabled by default**: Git hooks are local to your machine. This repository does not automatically install a pre-commit hook for you.
- **Requires a deployment backend**: `tools/auto_deploy_hook.py` expects a `wordpress_manager.py` / `wordpress_deployment_manager.py` module to exist in your environment. That module is not included in this repository snapshot.
- **Credentials must remain private**: store them outside git (e.g., `.deploy_credentials/sites.json` ignored by git, or environment variables).

## How it works

1. Detects staged files (`git diff --cached --name-only`)
2. Maps each file to a site key based on the top-level folder (see `SITE_MAPPING` in `tools/auto_deploy_hook.py`)
3. Calls the deployment backend to upload each file

## Usage

Dry run (no uploads):

```bash
python tools/auto_deploy_hook.py --dry-run
```

Deploy (requires deployment backend + credentials):

```bash
python tools/auto_deploy_hook.py --auto-deploy
```

## Pre-commit hook (local setup)

If you choose to wire this into git, verify whether you already have a pre-commit hook:

```bash
ls -la .git/hooks/pre-commit
```

## Configuration

- Adjust site mapping and supported paths in `tools/auto_deploy_hook.py` (`SITE_MAPPING`).
- Keep deployment credentials out of git.

