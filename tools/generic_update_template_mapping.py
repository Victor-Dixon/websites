#!/usr/bin/env python3
"""
Generic Template Mapping Updater
=================================

Updates page template mappings in functions.php for any WordPress site.
Works with all sites in site_configs.json.

Usage:
    python tools/generic_update_template_mapping.py --site example.com --page blog --template page-templates/page-blog-beautiful.php
    python tools/generic_update_template_mapping.py --site example.com --page streaming --template page-templates/page-streaming-beautiful.php --clear-cache

Agent-7: Web Development Specialist
"""

import argparse
import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from unified_wordpress_manager import UnifiedWordPressManager


def get_theme_name(manager: UnifiedWordPressManager) -> str:
    """Get active theme name using WP-CLI."""
    if not manager.deployer:
        return None
    
    if not manager.deployer.connect():
        return None
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        command = f"cd {remote_path} && wp theme list --status=active --format=json --allow-root"
        result = manager.deployer.execute_command(command)
        
        if result and result.strip() != "[]":
            import json
            try:
                themes = json.loads(result)
                if themes:
                    return themes[0].get('stylesheet', themes[0].get('name', ''))
            except:
                pass
        
        return None
    finally:
        manager.deployer.disconnect()


def update_template_mapping(manager: UnifiedWordPressManager, page_slug: str, template_path: str, clear_cache: bool = False) -> bool:
    """Update template mapping in functions.php."""
    if not manager.deployer:
        return False
    
    if not manager.deployer.connect():
        return False
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        theme_name = get_theme_name(manager)
        if not theme_name:
            print("⚠️  Could not determine theme name")
            return False
        
        functions_path = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
        
        # Read current functions.php
        with manager.deployer.sftp.open(functions_path, 'r') as f:
            functions_content = f.read().decode('utf-8')
        
        # Find the page_templates array
        pattern = r"(\$page_templates\s*=\s*array\s*\([^)]*\))"
        match = re.search(pattern, functions_content, re.DOTALL)
        
        if not match:
            print("⚠️  Could not find page_templates array")
            return False
        
        old_array = match.group(1)
        
        # Check if mapping already exists
        if f"'{page_slug}' => '{template_path}'" in old_array:
            print(f"ℹ️  Template mapping already exists for '{page_slug}'")
        else:
            # Add or update mapping
            # Remove old mapping if exists
            old_array = re.sub(
                rf"'{page_slug}'\s*=>\s*'[^']*',?\s*\n",
                '',
                old_array
            )
            
            # Add new mapping
            # Find the closing parenthesis
            if old_array.rstrip().endswith(');'):
                # Insert before closing
                new_array = old_array.rstrip()[:-2] + f"        '{page_slug}' => '{template_path}',  // Auto-mapped\n    );"
            else:
                # Add to array
                new_array = old_array.rstrip()
                if not new_array.endswith(','):
                    new_array += ','
                new_array += f"\n        '{page_slug}' => '{template_path}',  // Auto-mapped"
            
            # Replace in functions.php
            functions_content = functions_content.replace(old_array, new_array)
            
            # Write back
            with manager.deployer.sftp.open(functions_path, 'w') as f:
                f.write(functions_content.encode('utf-8'))
            
            print(f"✅ Updated template mapping: '{page_slug}' => '{template_path}'")
        
        # Clear cache if requested
        if clear_cache:
            print()
            print("🗑️  Clearing cache...")
            command = f"cd {remote_path} && wp cache flush --allow-root"
            result = manager.deployer.execute_command(command)
            if "Success" in result or "Cache" in result:
                print("✅ Cache cleared")
        
        return True
    except Exception as e:
        print(f"❌ Error updating template mapping: {e}")
        return False
    finally:
        manager.deployer.disconnect()


def main():
    """Main execution."""
    parser = argparse.ArgumentParser(description='Update template mapping for any WordPress site')
    parser.add_argument('--site', required=True, help='Site domain (e.g., example.com)')
    parser.add_argument('--page', required=True, help='Page slug (e.g., blog, streaming)')
    parser.add_argument('--template', required=True, help='Template path (e.g., page-templates/page-blog-beautiful.php)')
    parser.add_argument('--clear-cache', action='store_true', help='Clear WordPress cache after update')
    
    args = parser.parse_args()
    
    print("=" * 70)
    print(f"UPDATE TEMPLATE MAPPING - {args.site}")
    print("=" * 70)
    print()
    
    # Load site configs
    sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
    from simple_wordpress_deployer import load_site_configs
    site_configs = load_site_configs()
    
    if args.site not in site_configs:
        print(f"❌ Site {args.site} not found in site_configs.json")
        print(f"   Available sites: {', '.join(site_configs.keys())}")
        return
    
    # Initialize manager
    manager = UnifiedWordPressManager(args.site, site_configs.get(args.site))
    
    print(f"📝 Updating template mapping:")
    print(f"   Page: {args.page}")
    print(f"   Template: {args.template}")
    print()
    
    if update_template_mapping(manager, args.page, args.template, args.clear_cache):
        print()
        print("=" * 70)
        print("✅ UPDATE COMPLETE")
        print("=" * 70)
    else:
        print()
        print("=" * 70)
        print("❌ UPDATE FAILED")
        print("=" * 70)
        sys.exit(1)

if __name__ == "__main__":
    main()

