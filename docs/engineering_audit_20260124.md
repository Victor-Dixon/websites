# Engineering Audit 2026-01-24

Status: Snapshot audit (non-authoritative)

Next review trigger: SSOT mapping refactor

## Executive Summary
- Packages are the SSOT for shared WordPress code; sites define configuration; deployment scripts synchronize packages into live websites.
- CI/CD workflows deploy on push/schedule and support dry-run artifacts per site.
- The unified deployer hardcodes SSOT path mapping per domain with legacy fallbacks, increasing drift risk.
- Push-based deploy logic depends on git history/branch detection and can be brittle in CI edge cases.
- Pytest collection failed because the Ollama discovery test performed environment-dependent imports at module import time.

## Architecture Overview
- Packages provide versioned plugins/themes.
- Sites reference package versions via site configuration.
- Deployers map SSOT paths, collect deployable files, and ship via SFTP/REST.
- GitHub Actions orchestrates validation, deployment, and dry-run reporting.

## Quality Signals
- PHP syntax validation is part of the deploy workflow.
- Dry-run deployments create per-site artifacts.
- Tests can fail early if environment-dependent imports run at module import time.

## Risks & Vulnerabilities (Ranked)
1. SSOT path mapping embedded in code can drift from actual filesystem layout.
2. Deployment secrets in CI/CD represent a non-trivial blast radius if compromised.
3. Push-based deploy logic may misbehave in shallow clones or non-standard CI contexts.

## Tech Debt & Refactor Plan (Ranked)
1. Externalize SSOT path mapping into a config file with validation.
2. Normalize deployment scripts to use a shared WEBSITES_ROOT or config-based base path.
3. Move asset minification to build tooling rather than inline deployment steps.

## Quick Wins
- Document test bootstrap requirements and environment-dependent tests.
- Add a short SSOT mapping reference doc for contributors.
- Clarify allowed tasks per site in the registry glossary.

## Next 7 Days Plan
1. Define SSOT mapping schema and migrate path mapping to config.
2. Update deployment scripts to consume a shared base path variable.
3. Add test bootstrap contracts and skip guards for missing dependencies.
4. Scope deploy secrets using CI environments.

## Appendix: Commands Run
- ls
- rg --files -g 'package.json' -g 'pyproject.toml' -g 'requirements*.txt' -g 'Cargo.toml' -g 'go.mod'
- ls .github
- ls .github/workflows
- sed -n '1,200p' README.md
- sed -n '1,200p' .github/workflows/deploy.yml
- sed -n '1,200p' .github/workflows/deploy-dry-run.yml
- sed -n '1,200p' ops/deployment/unified_deployer.py
- sed -n '1,200p' ops/deployment/deploy_on_push.py
- sed -n '1,200p' config/sites_registry.json
- sed -n '1,200p' deployment/deploy.ps1
- pytest -q (failed: import-time dependency for Ollama integration)
