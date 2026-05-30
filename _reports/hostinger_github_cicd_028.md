# Hostinger GitHub CI/CD Lane 028

## Created

- `.github/workflows/deploy-freeride-hostinger.yml`
- `runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh`

## Required GitHub Secrets

Set these in GitHub repo:

- `HOSTINGER_HOST`
- `HOSTINGER_USER`
- `HOSTINGER_PORT`
- `HOSTINGER_WP_PLUGINS_DIR`
- `HOSTINGER_SSH_PRIVATE_KEY`

## Deploy Mode

Current workflow is manual:

- GitHub Actions
- Deploy FreeRideInvestor to Hostinger
- Run workflow
- Choose staging or production

Push-to-deploy is present but commented out until staging proof is clean.

## Result

STATUS=CREATED
