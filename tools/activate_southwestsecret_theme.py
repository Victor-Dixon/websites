#!/usr/bin/env python3
"""
Activate southwestsecret Chopped & Screwed DJ Theme
====================================================

Activates the restored southwestsecret theme on the WordPress site.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod
    MANAGER_AVAILABLE = True
except ImportError:
    MANAGER_AVAILABLE = False
    print("❌ unified_wordpress_manager not available")
    sys.exit(1)

import json

def main():
    """Activate southwestsecret theme."""
    print("=" * 70)
    print("🎵 ACTIVATING SOUTHWESTSECRET CHOPPED & SCREWED DJ THEME")
    print("=" * 70)
    print()
    
    # Load site configs
    config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
    if not config_path.exists():
        print("❌ site_configs.json not found")
        return 1
    
    with open(config_path, 'r', encoding='utf-8') as f:
        site_configs = json.load(f)
    
    # Initialize manager
    manager = UnifiedWordPressManager("southwestsecret.com", site_configs.get("southwestsecret.com", {}))
    
    print("1️⃣  Listing available themes...")
    themes = manager.list_themes(method=DeploymentMethod.WP_CLI)
    
    if not themes:
        print("   ⚠️  Could not list themes (may need SFTP/SSH access)")
        print("   💡 Manual activation needed via WordPress admin")
        return 1
    
    print(f"   ✅ Found {len(themes)} theme(s):")
    for theme in themes:
        status = "✅ ACTIVE" if theme.get('status') == 'active' else "⚪ Inactive"
        print(f"      - {theme.get('name', 'Unknown')} ({theme.get('stylesheet', 'unknown')}) {status}")
    
    print()
    print("2️⃣  Activating southwestsecret theme...")
    
    # Try different possible theme names
    theme_names = ["southwestsecret", "SouthWest Secret", "Southwest Secret"]
    activated = False
    
    for theme_name in theme_names:
        if manager.activate_theme(theme_name, method=DeploymentMethod.WP_CLI):
            print(f"   ✅ Theme '{theme_name}' activated successfully!")
            activated = True
            break
        else:
            print(f"   ⚠️  Could not activate '{theme_name}'")
    
    if not activated:
        print("   ❌ Could not activate theme automatically")
        print("   💡 Manual activation required:")
        print("      1. Log into WordPress admin (https://southwestsecret.com/wp-admin)")
        print("      2. Go to Appearance → Themes")
        print("      3. Find 'SouthWest Secret' theme")
        print("      4. Click 'Activate'")
        return 1
    
    print()
    print("=" * 70)
    print("✅ THEME ACTIVATION COMPLETE")
    print("=" * 70)
    print("The Chopped & Screwed DJ theme should now be active on the site.")
    print("Visit https://southwestsecret.com to see the restored theme.")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

