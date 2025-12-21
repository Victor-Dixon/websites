# WordPress deployment setup guide (direct upload tooling)

**Date**: 2025-11-30  
**Purpose**: Configure direct-to-host deployment tooling for WordPress theme updates

## Overview

Some scripts in `tools/` are designed to deploy changed files directly to hosting via SFTP/SSH. This requires:

- A deployment manager module (commonly referenced as `wordpress_manager.py`)
- A local-only credentials file (or environment variables)

> Note: In this repository snapshot, the referenced `wordpress_manager.py` module is not included. If you want to use direct deployment, provide that module (internal/private tooling) and ensure it is importable by the scripts.

## Credentials

Store credentials outside of git. A common convention is:

- `.deploy_credentials/sites.json` (git-ignored)

Example structure:

```json
{
  "freerideinvestor": {
    "host": "sftp.example.com",
    "username": "your-username",
    "password": "your-password",
    "port": 22,
    "remote_path": "/public_html/wp-content/themes/freerideinvestor"
  }
}
```

## Usage

- Deploy packages + manual upload (works without direct deployment tooling):
  - `python tools/deploy_website_fixes.py`
- If direct deployment is configured in your environment:
  - `python tools/deploy_all_websites.py`
  - `python tools/auto_deploy_hook.py --auto-deploy`

## Troubleshooting

- **Connection failed**: confirm host/port, credentials, and that SFTP/SSH access is enabled by your hosting provider.
- **Upload failed**: confirm `remote_path` and file permissions, and verify the target theme is the active theme.

