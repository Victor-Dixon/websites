# Site Configuration Files

This directory contains configuration files for website deployment and management.

## Files

### `site_configs.json` (DO NOT COMMIT)
Contains actual deployment credentials:
- SFTP connection details (host, username, password, port)
- WordPress REST API credentials (username, app_password)
- Deployment method settings
- SEO/UX deployment configurations

**⚠️ This file contains sensitive credentials and is excluded from git.**

### `sites_registry.json` (DO NOT COMMIT)
Contains site metadata and operational settings:
- Site mode (ACTIVE, WARM, MAINTENANCE, BUILDING)
- Purpose and ownership
- Allowed tasks
- Categories and default tags

**⚠️ This file may contain sensitive information and is excluded from git.**

## Example Files

### `site_configs.example.json`
Template file showing the structure of `site_configs.json` with placeholder values.
Copy this file to `site_configs.json` and fill in your actual credentials.

### `sites_registry.example.json`
Template file showing the structure of `sites_registry.json` with placeholder values.
Copy this file to `sites_registry.json` and fill in your site information.

## Setup Instructions

1. **Copy example files:**
   ```bash
   cp configs/site_configs.example.json configs/site_configs.json
   cp configs/sites_registry.example.json configs/sites_registry.json
   ```

2. **Fill in credentials:**
   - Edit `site_configs.json` with your SFTP and WordPress REST API credentials
   - Edit `sites_registry.json` with your site metadata

3. **Sync from deploy credentials (optional):**
   If you have credentials in `.deploy_credentials/` directory, you can use the sync script:
   ```bash
   python tools/sync_site_credentials.py
   ```

## Security Notes

- Never commit `site_configs.json` or `sites_registry.json` to git
- Keep example files in git for reference
- Use environment variables or secure credential storage for production
- Rotate credentials regularly

