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

import json
import subprocess
import sys
from pathlib import Path
from typing import Dict, List, Optional, Set

# Add deployment tools to path
DEPLOYMENT_TOOLS = Path(__file__).parent.parent / "ops" / "deployment"
if DEPLOYMENT_TOOLS.exists():
    sys.path.insert(0, str(DEPLOYMENT_TOOLS))

# Try to use SimpleWordPressDeployer (local, preferred)
try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    WordPressDeploymentManager = SimpleWordPressDeployer
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False
    # Fallback: Try main repo tools
    MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
    if MAIN_REPO_TOOLS.exists():
        sys.path.insert(0, str(MAIN_REPO_TOOLS))
    
    try:
        from unified_wordpress_manager import UnifiedWordPressManager
        # Create adapter class for compatibility
        class WordPressDeploymentManagerAdapter:
            def __init__(self, site_key: str):
                self.manager = UnifiedWordPressManager(site_key)
                self.site_key = site_key
            
            def deploy_file(self, local_path: Path) -> bool:
                from tools.unified_wordpress_manager import DeploymentMethod
                return self.manager.deploy_file(local_path, method=DeploymentMethod.SFTP)
            
            def close(self):
                if hasattr(self.manager, 'deployer') and self.manager.deployer:
                    self.manager.deployer.disconnect()
        
        WordPressDeploymentManager = WordPressDeploymentManagerAdapter
        DEPLOYER_AVAILABLE = True
    except ImportError:
        print("⚠️  WARNING: No WordPress deployer found!")
        print("   Tried: simple_wordpress_deployer, unified_wordpress_manager")
        print("   Deployment will be skipped. Files will still be committed.")
        DEPLOYER_AVAILABLE = False


# Site mapping: local directory → site key
SITE_MAPPING = {
    "FreeRideInvestor": "freerideinvestor",
    "southwestsecret.com": "southwestsecret",
    "Swarm_website": "weareswarm",
    "TradingRobotPlugWeb": "tradingrobotplug",
    "prismblossom.online": "prismblossom",
}

# New canonical layout support: websites/<domain>/...
# This keeps legacy mappings intact while allowing future migrations without breaking deploy detection.
DOMAIN_SITE_KEY_OVERRIDES = {
    # Canonical domain -> deploy manager key (legacy)
    "freerideinvestor.com": "freerideinvestor",
    "southwestsecret.com": "southwestsecret",
    "weareswarm.site": "weareswarm",
    "prismblossom.online": "prismblossom",
}

# File type mappings
THEME_FILES = {
    "style.css", "functions.php", "header.php", "footer.php", "index.php",
    "front-page.php", "page-*.php", "single.php", "archive.php", "*.js", "*.css"
}
PLUGIN_FILES = {"*.php", "*.js", "*.css"}


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


def is_theme_file(file_path: str) -> bool:
    """Check if file is a theme file."""
    path = Path(file_path)
    
    # Check if in theme directory
    if "theme" in path.parts or "themes" in path.parts:
        return True
    
    # Check file extension
    if path.suffix in [".php", ".css", ".js"]:
        # Check if it's in a site root (likely theme file)
        for site_dir in SITE_MAPPING.keys():
            if site_dir in path.parts and path.parent.name == site_dir:
                return True
    
    return False


def is_plugin_file(file_path: str) -> bool:
    """Check if file is a plugin file."""
    return "plugin" in Path(file_path).parts or "plugins" in Path(file_path).parts


def get_relative_theme_path(file_path: str, site_key: str) -> Optional[str]:
    """Get relative path within theme directory (file name or subpath)."""
    path = Path(file_path)
    parts = list(path.parts)
    
    # Find site directory
    site_dir = None
    for local_dir, key in SITE_MAPPING.items():
        if key == site_key:
            site_dir = local_dir
            break
    
    if not site_dir or site_dir not in parts:
        return None
    
    # Find site directory index
    site_idx = None
    for i, part in enumerate(parts):
        if part == site_dir:
            site_idx = i
            break
    
    if site_idx is None:
        return None
    
    # Look for theme directory structure
    # Pattern 1: SiteDir/wordpress-theme/theme-name/file
    # Pattern 2: SiteDir/wp-content/themes/theme-name/file
    # Pattern 3: SiteDir/file (root theme file)
    
    # Check for wordpress-theme or wp-content/themes
    theme_name_idx = None
    for i in range(site_idx + 1, len(parts)):
        part = parts[i].lower()
        if "theme" in part:
            # Found theme directory, next should be theme name
            if i + 1 < len(parts):
                theme_name_idx = i + 1
                break
        elif part in ["wp-content"]:
            # Check next for themes
            if i + 1 < len(parts) and "theme" in parts[i + 1].lower():
                if i + 2 < len(parts):
                    theme_name_idx = i + 2
                    break
    
    if theme_name_idx is not None and theme_name_idx + 1 < len(parts):
        # Return path after theme name
        return "/".join(parts[theme_name_idx + 1:])
    elif site_idx + 1 < len(parts):
        # Root theme file (directly in site dir or one level down)
        # Skip known non-theme directories
        skip_dirs = {"wordpress-theme", "wp-content", "wp-themes", "plugins", "wp-plugins"}
        start_idx = site_idx + 1
        if parts[start_idx] in skip_dirs:
            # Skip this directory and theme name if present
            if start_idx + 2 < len(parts):
                return "/".join(parts[start_idx + 2:])
            else:
                return path.name
        else:
            # Direct file in site root
            return "/".join(parts[start_idx:])
    else:
        return path.name


def deploy_file_to_site(file_path: str, site_key: str) -> bool:
    """Deploy a single file to the appropriate site."""
    if not DEPLOYER_AVAILABLE:
        print(f"⚠️  Skipping deployment: No deployer available")
        return True  # Don't fail commit if deployer unavailable
    
    try:
        # Load site configs using proper priority order (env vars → sites.json → site_configs.json)
        if WordPressDeploymentManager == SimpleWordPressDeployer:
            # Use load_site_configs() which checks all credential sources in priority order
            site_configs = load_site_configs()
            if not site_configs:
                print(f"⚠️  No site configurations found for {site_key}")
                return False
            manager = WordPressDeploymentManager(site_key, site_configs)
        else:
            # UnifiedWordPressManager adapter
            manager = WordPressDeploymentManager(site_key)
        
        local_path = Path(__file__).parent.parent / file_path
        if not local_path.exists():
            print(f"⚠️  File not found: {local_path}")
            return False
        
        # Connect before deploying
        if hasattr(manager, 'connect'):
            if not manager.connect():
                print(f"⚠️  Could not connect to {site_key}")
                return False
        
        # Use deploy_file method
        success = manager.deploy_file(local_path)
        
        # Cleanup
        if hasattr(manager, 'close'):
            manager.close()
        elif hasattr(manager, 'disconnect'):
            manager.disconnect()
        
        return success
        
    except Exception as e:
        print(f"❌ Error deploying {file_path} to {site_key}: {e}")
        import traceback
        traceback.print_exc()
        return False


def auto_deploy() -> bool:
    """Auto-deploy changed files to appropriate websites."""
    print("=" * 70)
    print("🚀 AUTO-DEPLOYMENT: Detecting changed files...")
    print("=" * 70)
    print()
    
    # Get changed files
    changed_files = get_changed_files()
    
    if not changed_files:
        print("✅ No files to deploy (no changes staged)")
        return True
    
    print(f"📋 Found {len(changed_files)} changed file(s):")
    for f in changed_files:
        print(f"   - {f}")
    print()
    
    # Group files by site
    files_by_site: Dict[str, List[str]] = {}
    skipped_files: List[str] = []
    
    for file_path in changed_files:
        site_key = detect_site_from_path(file_path)
        if site_key:
            if site_key not in files_by_site:
                files_by_site[site_key] = []
            files_by_site[site_key].append(file_path)
        else:
            skipped_files.append(file_path)
    
    if skipped_files:
        print("⚠️  Files not mapped to any site (skipped):")
        for f in skipped_files:
            print(f"   - {f}")
        print()
    
    if not files_by_site:
        print("⚠️  No files mapped to sites. Nothing to deploy.")
        return True
    
    # Deploy files to each site
    results: Dict[str, Dict[str, int]] = {}
    
    for site_key, files in files_by_site.items():
        print(f"🌐 Deploying to {site_key} ({len(files)} file(s))...")
        print("-" * 70)
        
        success_count = 0
        fail_count = 0
        
        for file_path in files:
            if deploy_file_to_site(file_path, site_key):
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


if __name__ == "__main__":
    import argparse
    parser = argparse.ArgumentParser(description="Auto-deploy changed files to websites")
    parser.add_argument("--auto-deploy", action="store_true", help="Run auto-deployment")
    parser.add_argument("--dry-run", action="store_true", help="Show what would be deployed without deploying")
    args = parser.parse_args()
    
    if args.dry_run:
        changed_files = get_changed_files()
        print("🔍 DRY RUN: Files that would be deployed:")
        for f in changed_files:
            site = detect_site_from_path(f)
            print(f"   {f} → {site or 'UNMAPPED'}")
    elif args.auto_deploy:
        success = auto_deploy()
        sys.exit(0 if success else 1)
    else:
        parser.print_help()
