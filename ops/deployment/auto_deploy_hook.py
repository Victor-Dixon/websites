#!/usr/bin/env python3
"""
Auto-Deployment Hook Script
===========================

Triggered by pre-commit hook to deploy changes to live websites.
Detects changed files and deploys them to appropriate WordPress sites.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-19
V2 Compliant: <400 lines
"""

from __future__ import annotations

import argparse
import subprocess
import sys
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, List, Optional

# NOTE: Do NOT import deployer modules at import-time.
# The hook must be safe in environments where deploy dependencies are absent.


@dataclass(frozen=True)
class DeployClassification:
    deployable_by_site: dict[str, list[str]]
    skipped_by_rules: list[str]
    unmapped: list[str]


# Site mapping: local directory → site key
# NOTE: Legacy root-level directories archived/moved to websites/
SITE_MAPPING = {
    # Legacy mappings removed - all sites now in websites/<domain>/
    # "FreeRideInvestor": "freerideinvestor",  # Archived to archive/FreeRideInvestor/
    # "southwestsecret.com": "southwestsecret",  # Moved to websites/southwestsecret.com/
    # "Swarm_website": "weareswarm",  # Moved to websites/weareswarm.site/
    # TradingRobotPlugWeb removed: now ignored and uses canonical websites/tradingrobotplug.com/ layout
    "prismblossom.online": "prismblossom",  # Still at root (legacy)
}

# New canonical layout support: websites/<domain>/...
# This keeps legacy mappings intact while allowing future migrations without breaking deploy detection.
DOMAIN_SITE_KEY_OVERRIDES = {
    # Canonical domain -> deploy manager key (legacy)
    "freerideinvestor.com": "freerideinvestor",
    "dadudekc.com": "dadudekc.com",
    "southwestsecret.com": "southwestsecret",
    "weareswarm.site": "weareswarm",
    "prismblossom.online": "prismblossom",
}

SKIP_BASENAMES = {".gitignore", ".gitmodules"}
SKIP_PREFIXES = {"agent_workspaces/", "docs/", "src/autoblogger/ssot/", "tools/"}
REPO_ROOT = Path(__file__).resolve().parents[2]


def _safe_import_deployer():
    """
    Resolve a deployer implementation if available.
    Returns (deployer_factory, label) or (None, label) for no-deployer mode.
    Never raises at import-time.
    """
    # Preferred: ops SSOT deployer (if present)
    try:
        from ops.deployment.wordpress_manager import WordPressDeploymentManager as _Mgr  # type: ignore
        from ops.deployment.wordpress_manager import load_site_configs as _load  # type: ignore
        return (lambda site_key: _Mgr(site_key, _load()), "ops.wordpress_manager")
    except Exception:
        pass

    # Legacy fallback: tools/unified_wordpress_manager.py (common repo snapshot)
    try:
        from tools.unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod  # type: ignore

        class _Adapter:
            def __init__(self, site_key: str):
                self._mgr = UnifiedWordPressManager(site_key)
                self._site_key = site_key

            def deploy_file(self, local_path: Path) -> bool:
                return self._mgr.deploy_file(local_path, method=DeploymentMethod.SFTP)

            def close(self):
                try:
                    if hasattr(self._mgr, "deployer") and self._mgr.deployer:
                        self._mgr.deployer.disconnect()
                except Exception:
                    pass

        return (lambda site_key: _Adapter(site_key), "tools.unified_wordpress_manager")
    except Exception:
        pass

    return (None, "none")


def normalize_path(file_path: str) -> str:
    """Normalize a file path for prefix checks."""
    return file_path.replace("\\", "/")


def should_skip_path(file_path: str) -> bool:
    """Check if a path should be skipped for deployment."""
    normalized = normalize_path(file_path)
    if Path(normalized).name in SKIP_BASENAMES:
        return True
    if any(normalized.startswith(prefix) for prefix in SKIP_PREFIXES):
        return True
    local_path = REPO_ROOT / file_path
    return local_path.is_dir()


def get_changed_files() -> List[str]:
    """Get list of changed files from git staging area."""
    try:
        result = subprocess.run(
            ["git", "diff", "--cached", "--name-only", "--diff-filter=ACMR"],
            capture_output=True,
            text=True,
            check=True,
            cwd=Path(__file__).parent.parent
        )
        files = [f.strip() for f in result.stdout.splitlines() if f.strip()]
        return files
    except subprocess.CalledProcessError as e:
        print(f"⚠️  Error getting changed files: {e}")
        return []


def detect_site_from_path(file_path: str) -> Optional[str]:
    """Detect which site a file belongs to based on path."""
    path_parts = Path(file_path).parts

    # Canonical layout: websites/<domain>/...
    # Example: websites/freerideinvestor.com/wp/wp-content/themes/foo/style.css
    if "websites" in path_parts:
        try:
            idx = path_parts.index("websites")
            if idx + 1 < len(path_parts):
                domain = path_parts[idx + 1]
                # Minimal sanity check: domain-like string
                if "." in domain:
                    # Only deploy sites we explicitly mapped to a deployment key.
                    # This prevents accidental deploy attempts for domains not yet configured.
                    return DOMAIN_SITE_KEY_OVERRIDES.get(domain)
        except ValueError:
            pass

    # Check each site mapping
    for local_dir, site_key in SITE_MAPPING.items():
        if local_dir in path_parts:
            return site_key

    return None



def classify_changed_files(changed_files: list[str]) -> DeployClassification:
    deployable_by_site: dict[str, list[str]] = {}
    skipped_by_rules: list[str] = []
    unmapped: list[str] = []

    for file_path in changed_files:
        # Apply skip rules FIRST (basenames, prefixes, directories)
        if should_skip_path(file_path):
            skipped_by_rules.append(file_path)
            continue

        site_key = detect_site_from_path(file_path)
        if site_key:
            deployable_by_site.setdefault(site_key, []).append(file_path)
        else:
            unmapped.append(file_path)

    return DeployClassification(
        deployable_by_site=deployable_by_site,
        skipped_by_rules=skipped_by_rules,
        unmapped=unmapped,
    )


def deploy_file_to_site(file_path: str, site_key: str, deployer_factory) -> bool:
    """Deploy a single file to the appropriate site."""
    try:
        manager = deployer_factory(site_key)

        local_path = REPO_ROOT / file_path
        if not local_path.exists():
            print(f"⚠️  File not found: {local_path}")
            return False

        success = manager.deploy_file(local_path)
        if hasattr(manager, "close"):
            manager.close()
        return bool(success)
    except Exception as e:
        print(f"❌ Error deploying {file_path} to {site_key}: {e}")
        return False


def auto_deploy() -> bool:
    """Auto-deploy changed files to appropriate websites."""
    print("=" * 70)
    print("🚀 AUTO-DEPLOYMENT: Detecting changed files...")
    print("=" * 70)
    print()

    changed_files = get_changed_files()

    if not changed_files:
        print("✅ No files to deploy (no changes staged)")
        return True

    print(f"📋 Found {len(changed_files)} changed file(s):")
    for f in changed_files:
        print(f"   - {f}")
    print()

    classified = classify_changed_files(changed_files)
    files_by_site = classified.deployable_by_site
    skipped_files = classified.skipped_by_rules
    unmapped_files = classified.unmapped

    if skipped_files:
        print("🧹 Skipped by rules (ignored basenames/prefixes/dirs):")
        for f in skipped_files:
            print(f"   - {f}")
        print()

    if unmapped_files:
        print("⚠️  Unmapped (not associated with a deployable site):")
        for f in unmapped_files:
            print(f"   - {f}")
        print()

    if not files_by_site:
        print("⚠️  No files mapped to sites. Nothing to deploy.")
        return True

    deployer_factory, deployer_label = _safe_import_deployer()
    if deployer_factory is None:
        print(f"⚠️  No deployer available ({deployer_label}). Skipping deployment to avoid blocking commits.")
        return True

    # Deploy files to each site
    results: Dict[str, Dict[str, int]] = {}

    for site_key, files in files_by_site.items():
        print(f"🌐 Deploying to {site_key} ({len(files)} file(s))...")
        print("-" * 70)

        success_count = 0
        fail_count = 0

        for file_path in files:
            if deploy_file_to_site(file_path, site_key, deployer_factory):
                success_count += 1
            else:
                fail_count += 1

        results[site_key] = {
            "success": success_count,
            "failed": fail_count,
            "total": len(files)
        }

        print(f"✅ {site_key}: {success_count} succeeded, {fail_count} failed")
        print()

    # Summary
    print("=" * 70)
    print("📊 DEPLOYMENT SUMMARY")
    print("=" * 70)
    total_success = sum(r["success"] for r in results.values())
    total_failed = sum(r["failed"] for r in results.values())
    total_files = sum(r["total"] for r in results.values())

    print(f"Total files: {total_files}")
    print(f"✅ Succeeded: {total_success}")
    print(f"❌ Failed: {total_failed}")
    print()

    if total_failed > 0:
        print("⚠️  Some files failed to deploy. Check errors above.")
        return False

    print("✅ All files deployed successfully!")
    return True


def dry_run() -> int:
    changed_files = get_changed_files()
    if not changed_files:
        print("✅ DRY RUN: No staged changes.")
        return 0

    classified = classify_changed_files(changed_files)

    print("🔍 DRY RUN: Deployment preview (matches real skip rules)")
    print("-" * 70)

    if classified.skipped_by_rules:
        print("🧹 Skipped by rules:")
        for f in classified.skipped_by_rules:
            print(f"   - {f}")
        print()

    if classified.unmapped:
        print("⚠️  Unmapped:")
        for f in classified.unmapped:
            print(f"   - {f}")
        print()

    if not classified.deployable_by_site:
        print("✅ Nothing would be deployed.")
        return 0

    print("🌐 Would deploy:")
    for site_key, files in classified.deployable_by_site.items():
        print(f"  - {site_key}:")
        for f in files:
            print(f"      • {f}")
    return 0


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Auto-deploy changed files to websites")
    parser.add_argument("--auto-deploy", action="store_true", help="Run auto-deployment")
    parser.add_argument("--dry-run", action="store_true", help="Show what would be deployed without deploying")
    args = parser.parse_args()

    if args.dry_run:
        raise SystemExit(dry_run())
    if args.auto_deploy:
        success = auto_deploy()
        sys.exit(0 if success else 1)
    parser.print_help()
