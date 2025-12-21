# Website deployment status

**Date**: 2025-11-30

## Current state

- **Packaging-based deployment** is supported via this repository:
  - Generate packages/instructions: `python tools/deploy_website_fixes.py`
  - Output directory: `tools/deployment_packages/`
- **Direct-to-host deployment** scripts exist (e.g., `tools/deploy_all_websites.py`, `tools/auto_deploy_hook.py`), but they reference an external `wordpress_manager.py` component that is not present in this repository snapshot. If you intend to use direct deployment, you will need to provide that component and configure credentials.

## Credentials

This repository does not store production credentials. If you use any direct deployment tooling, keep credentials in a local-only, git-ignored file (commonly `.deploy_credentials/sites.json`) or inject them via environment variables.

## Next steps

- **Option A (recommended / lowest risk)**: deploy via package + manual upload (WordPress admin editor or SFTP).
- **Option B**: enable direct deployment after providing the missing deployment manager and credential configuration.

## Success criteria

- Files uploaded to the intended theme directory
- Site loads without errors
- Expected UI/behavior changes are visible after cache clearing

