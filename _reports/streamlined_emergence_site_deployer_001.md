# Streamlined Emergence Site Deployer 001

## Task
Fix the deployment workflow and make future website deploys easier.

## Actions Taken
- Added durable deployer:
  - `runtime/scripts/dreamos_site_deployer.py`
- Added task artifact:
  - `runtime/tasks/emergence/add_streamlined_emergence_site_deployer_001.yaml`
- Deployer supports:
  - `auto`
  - `static`
  - `wp-page`
- Deployer detects WordPress by checking remote `wp-config.php`.
- If WordPress exists, it strips standalone wrappers and upserts a WP page.
- If WordPress does not exist, it uploads a static HTML file.
- Deployed Emergence preview using auto mode.

## Target
- `https://maskzero.site/emergence-preview/`

## Verification
```text
DEPLOYER_SYNTAX=PASS
DEPLOYER_HELP=PASS
HOSTINGER_ENV_FOUND=PASS
AUTO_DEPLOY=PASS
HTTP_VERIFY=PASS
```

## Commit Message
Add streamlined website deployer

## Status
PASS
