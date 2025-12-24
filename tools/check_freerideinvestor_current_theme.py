#!/usr/bin/env python3
"""
Check freerideinvestor.com Current Theme
========================================

Checks what theme is currently active and looks for old theme backups.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()

deployer_paths = [
    Path(__file__).parent.parent / "ops" / "deployment",
]
for path in deployer_paths:
    if (path / "simple_wordpress_deployer.py").exists():
        sys.path.insert(0, str(path))
        break

from simple_wordpress_deployer import SimpleWordPressDeployer


def load_site_configs():
    """Load site configurations."""
    config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
    if config_path.exists():
        import json
        with open(config_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {}


def check_current_theme():
    """Check current theme and look for old themes."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 CHECKING CURRENT THEME: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        
        # 1. Check active theme
        print("1️⃣ Checking active theme...")
        get_theme_cmd = f"cd {remote_path} && wp theme list --status=active --allow-root 2>&1"
        theme_result = deployer.execute_command(get_theme_cmd)
        print(theme_result)
        print()
        
        # 2. List all available themes
        print("2️⃣ Listing all available themes...")
        list_themes_cmd = f"cd {remote_path} && wp theme list --allow-root 2>&1"
        all_themes = deployer.execute_command(list_themes_cmd)
        print(all_themes)
        print()
        
        # 3. Check theme directory for backups
        print("3️⃣ Checking theme directory for backups/old themes...")
        themes_dir = f"{remote_path}/wp-content/themes"
        list_themes_dir = f"ls -la {themes_dir}/ 2>&1"
        themes_list = deployer.execute_command(list_themes_dir)
        print(themes_list)
        print()
        
        # 4. Check for theme backups in parent directory
        print("4️⃣ Checking for theme backups...")
        check_backups = f"find {remote_path} -type d -name '*backup*' -o -name '*old*' -o -name '*bak*' 2>&1 | head -10"
        backups = deployer.execute_command(check_backups)
        if backups.strip():
            print(backups)
        else:
            print("   No obvious backup directories found")
        print()
        
        # 5. Check active theme files
        get_active_cmd = f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>&1"
        active_theme = deployer.execute_command(get_active_cmd).strip()
        
        if active_theme and active_theme != 'error':
            print(f"5️⃣ Examining active theme: {active_theme}")
            theme_path = f"{themes_dir}/{active_theme}"
            
            # List theme files
            list_files = f"ls -la {theme_path}/ 2>&1"
            files = deployer.execute_command(list_files)
            print("   Theme files:")
            print(files)
            print()
            
            # Check style.css header
            check_style = f"head -20 {theme_path}/style.css 2>&1"
            style_header = deployer.execute_command(check_style)
            print("   style.css header:")
            print(style_header)
            print()
            
            # Check index.php
            check_index = f"head -30 {theme_path}/index.php 2>&1"
            index_content = deployer.execute_command(check_index)
            print("   index.php preview:")
            print(index_content)
            print()
        
        # 6. Check local repository for old theme files
        print("6️⃣ Checking local repository for old theme files...")
        local_site_path = Path(__file__).parent.parent / "websites" / site_name
        if local_site_path.exists():
            print(f"   Local path: {local_site_path}")
            # Look for theme directories
            for item in local_site_path.rglob("*"):
                if item.is_dir() and ('theme' in item.name.lower() or 'template' in item.name.lower()):
                    print(f"   Found: {item}")
        else:
            print(f"   Local path not found: {local_site_path}")
        print()
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = check_current_theme()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

