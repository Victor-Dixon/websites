#!/usr/bin/env python3
"""
Verify Website Themes Migration
================================

Checks if all website themes have been moved from main repo to websites repo.
Uses GitHub API to verify repository contents.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-19
V2 Compliant: <400 lines
"""

import os
import subprocess
import sys
from pathlib import Path
from typing import Dict, List, Set

try:
    from dotenv import load_dotenv
except ImportError:
    print("❌ python-dotenv not found! Installing...")
    subprocess.run([sys.executable, "-m", "pip", "install", "python-dotenv"], check=True)
    from dotenv import load_dotenv

try:
    import requests
except ImportError:
    print("❌ requests not found! Installing...")
    subprocess.run([sys.executable, "-m", "pip", "install", "requests"], check=True)
    import requests

# Load environment variables
PROJECT_ROOT = Path("D:/Agent_Cellphone_V2_Repository")
ENV_FILE = PROJECT_ROOT / ".env"
if ENV_FILE.exists():
    load_dotenv(ENV_FILE)

# Expected themes in websites repo (canonical layout)
EXPECTED_THEMES = {
    "websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern": "freerideinvestor",  # ✅ MIGRATED 2025-12-20 (Phases 1 & 2)
    "websites/southwestsecret.com/wp/wp-content/themes/southwestsecret": "southwestsecret",  # ✅ MIGRATED 2025-12-20
    "websites/weareswarm.site/wp/wp-content/themes/swarm-theme": "weareswarm",  # ✅ MIGRATED 2025-12-20
    "websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme": "tradingrobotplug",  # ✅ MIGRATED 2025-12-20
}

# Legacy theme locations (for backward compatibility check)
LEGACY_THEME_LOCATIONS = {
    "FreeRideInvestor/wp-content/themes/freerideinvestor-modern": "freerideinvestor",
    "southwestsecret.com/wordpress-theme/southwestsecret": "southwestsecret",  # Legacy - preserved
    "Swarm_website/wp-content/themes/swarm-theme": "weareswarm",
    "TradingRobotPlugWeb/wordpress/wp-content/themes/my-custom-theme": "tradingrobotplug",  # Legacy - preserved (renamed to tradingrobotplug-theme in canonical)
}

# Known theme locations in main repo (should be moved or removed)
MAIN_REPO_THEME_LOCATIONS = [
    "FreerideinvestorWebsite/freerideinvestor-theme",
    "temp_themes",
    "tools/temp_themes",
    "docs/toolbelt/theme_deployment",
]


def get_github_token() -> str:
    """Get GitHub token from environment."""
    token = os.getenv("GITHUB_TOKEN")
    if not token:
        raise ValueError("GITHUB_TOKEN not found in environment or .env file")
    return token


def check_local_websites_repo() -> Dict[str, bool]:
    """Check which themes exist locally in websites repo."""
    websites_repo = Path("D:/websites")
    results = {}
    
    for theme_path, site_key in EXPECTED_THEMES.items():
        full_path = websites_repo / theme_path
        exists = full_path.exists()
        results[theme_path] = exists
        if exists:
            # Check if it has actual theme files
            php_files = list(full_path.rglob("*.php"))
            css_files = list(full_path.rglob("*.css"))
            has_content = len(php_files) > 0 or len(css_files) > 0
            results[f"{theme_path}_has_content"] = has_content
    
    return results


def check_main_repo_themes() -> Dict[str, bool]:
    """Check which theme locations still exist in main repo."""
    main_repo = Path("D:/Agent_Cellphone_V2_Repository")
    results = {}
    
    for theme_location in MAIN_REPO_THEME_LOCATIONS:
        full_path = main_repo / theme_location
        exists = full_path.exists()
        results[theme_location] = exists
        if exists:
            # Check if it has actual theme files
            php_files = list(full_path.rglob("*.php"))
            css_files = list(full_path.rglob("*.css"))
            has_content = len(php_files) > 0 or len(css_files) > 0
            results[f"{theme_location}_has_content"] = has_content
    
    return results


def check_github_repo_contents(repo_name: str = "websites") -> Dict[str, any]:
    """Check GitHub repository contents using API."""
    token = get_github_token()
    username = os.getenv("GITHUB_USERNAME", "Dadudekc")
    
    url = f"https://api.github.com/repos/{username}/{repo_name}/contents"
    headers = {
        "Authorization": f"token {token}",
        "Accept": "application/vnd.github.v3+json"
    }
    
    try:
        response = requests.get(url, headers=headers, timeout=10)
        if response.status_code == 200:
            contents = response.json()
            # Extract directory names
            directories = [item["name"] for item in contents if item["type"] == "dir"]
            return {
                "success": True,
                "directories": directories,
                "total_items": len(contents)
            }
        else:
            return {
                "success": False,
                "error": f"HTTP {response.status_code}: {response.text}"
            }
    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }


def generate_migration_report() -> str:
    """Generate comprehensive migration report."""
    print("=" * 70)
    print("🔍 WEBSITE THEMES MIGRATION VERIFICATION")
    print("=" * 70)
    print()
    
    # Check local websites repo
    print("1️⃣  Checking local websites repository...")
    local_results = check_local_websites_repo()
    
    missing_themes = []
    present_themes = []
    
    for theme_path, exists in local_results.items():
        if "_has_content" not in theme_path:
            if exists and local_results.get(f"{theme_path}_has_content", False):
                present_themes.append(theme_path)
                print(f"   ✅ {theme_path} - Present with content")
            elif exists:
                print(f"   ⚠️  {theme_path} - Present but empty")
            else:
                missing_themes.append(theme_path)
                print(f"   ❌ {theme_path} - MISSING")
    
    print()
    
    # Check main repo for leftover themes
    print("2️⃣  Checking main repository for leftover themes...")
    main_repo_results = check_main_repo_themes()
    
    leftover_themes = []
    for theme_location, exists in main_repo_results.items():
        if "_has_content" not in theme_location:
            if exists and main_repo_results.get(f"{theme_location}_has_content", False):
                leftover_themes.append(theme_location)
                print(f"   ⚠️  {theme_location} - Still exists with content (should be moved/removed)")
            elif exists:
                print(f"   ℹ️  {theme_location} - Exists but empty (can be removed)")
            else:
                print(f"   ✅ {theme_location} - Not found (good)")
    
    print()
    
    # Check GitHub repo
    print("3️⃣  Checking GitHub repository...")
    github_results = check_github_repo_contents()
    
    if github_results["success"]:
        print(f"   ✅ GitHub repo accessible")
        print(f"   📁 Found {github_results['total_items']} items")
        print(f"   📂 Directories: {', '.join(github_results['directories'][:10])}")
        if len(github_results['directories']) > 10:
            print(f"   ... and {len(github_results['directories']) - 10} more")
    else:
        print(f"   ❌ GitHub check failed: {github_results.get('error', 'Unknown error')}")
    
    print()
    
    # Summary
    print("=" * 70)
    print("📊 MIGRATION SUMMARY")
    print("=" * 70)
    print()
    print(f"✅ Themes in websites repo: {len(present_themes)}/{len(EXPECTED_THEMES)}")
    print(f"❌ Missing themes: {len(missing_themes)}")
    print(f"⚠️  Leftover themes in main repo: {len(leftover_themes)}")
    print()
    
    if missing_themes:
        print("❌ MISSING THEMES (need to be moved):")
        for theme in missing_themes:
            print(f"   - {theme}")
        print()
    
    if leftover_themes:
        print("⚠️  LEFTOVER THEMES IN MAIN REPO (should be moved/removed):")
        for theme in leftover_themes:
            print(f"   - {theme}")
        print()
    
    if not missing_themes and not leftover_themes:
        print("✅ All themes successfully migrated!")
        print()
    
    return "Report generated"


if __name__ == "__main__":
    try:
        generate_migration_report()
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)

