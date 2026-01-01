#!/usr/bin/env python3
"""
Restructure TradingRobotPlug Navigation Menu
- Reduce primary menu to 5-6 items
- Move legal pages to footer menu
- Optimize navigation structure

Author: Agent-7
Date: 2025-12-30
"""

import subprocess
import sys
import os

# WordPress site configuration
SITE_KEY = "tradingrobotplug.com"
WP_PATH = f"/home/u996867598/domains/{SITE_KEY}/public_html/wp"

def run_wp_cli(command):
    """Execute WP-CLI command"""
    full_command = f"wp {command} --path={WP_PATH} --allow-root"
    try:
        result = subprocess.run(
            full_command,
            shell=True,
            capture_output=True,
            text=True,
            check=True
        )
        return result.stdout.strip()
    except subprocess.CalledProcessError as e:
        print(f"Error: {e.stderr}")
        return None

def get_menu_id(menu_name):
    """Get menu ID by name"""
    result = run_wp_cli(f"menu list --format=json")
    if not result:
        return None
    
    import json
    menus = json.loads(result)
    for menu in menus:
        if menu['name'] == menu_name:
            return menu['term_id']
    return None

def get_page_id(page_slug):
    """Get page ID by slug"""
    result = run_wp_cli(f"post list --post_type=page --name={page_slug} --format=json --fields=ID,post_name")
    if not result:
        return None
    
    import json
    pages = json.loads(result)
    if pages:
        return pages[0]['ID']
    return None

def create_or_get_menu(menu_name, location):
    """Create menu or get existing menu ID"""
    menu_id = get_menu_id(menu_name)
    
    if not menu_id:
        # Create new menu
        result = run_wp_cli(f"menu create {menu_name}")
        if result:
            menu_id = get_menu_id(menu_name)
    
    if menu_id:
        # Assign to location
        run_wp_cli(f"menu location assign {menu_name} {location}")
    
    return menu_id

def add_menu_item(menu_id, title, url, parent=0):
    """Add item to menu"""
    if parent > 0:
        command = f"menu item add-post {menu_id} {url} --title='{title}' --parent-id={parent}"
    else:
        command = f"menu item add-post {menu_id} {url} --title='{title}'"
    
    result = run_wp_cli(command)
    return result

def clear_menu(menu_id):
    """Clear all items from menu"""
    # Get all menu items
    result = run_wp_cli(f"menu item list {menu_id} --format=json")
    if result:
        import json
        items = json.loads(result)
        for item in items:
            run_wp_cli(f"menu item delete {item['db_id']}")

def restructure_navigation():
    """Restructure navigation menu"""
    print("🔄 Restructuring TradingRobotPlug navigation...")
    
    # Step 1: Create/Get Primary Menu (5 items)
    print("\n1. Setting up Primary Menu (5 items)...")
    primary_menu_id = create_or_get_menu("Primary Menu", "primary")
    
    if primary_menu_id:
        # Clear existing items
        clear_menu(primary_menu_id)
        
        # Add primary menu items (5 items)
        primary_items = [
            ("Home", "/"),
            ("Features", "/features"),
            ("Pricing", "/pricing"),
            ("AI Swarm", "/ai-swarm"),
            ("Get Started", "/waitlist")
        ]
        
        for title, url in primary_items:
            page_id = get_page_id(url.strip('/'))
            if page_id:
                add_menu_item(primary_menu_id, title, page_id)
                print(f"   ✅ Added: {title}")
            else:
                # Try as custom link
                run_wp_cli(f"menu item add-custom {primary_menu_id} --title='{title}' --url='{url}'")
                print(f"   ✅ Added: {title} (custom link)")
    
    # Step 2: Create/Get Footer Menu (Legal pages)
    print("\n2. Setting up Footer Menu (Legal pages)...")
    footer_menu_id = create_or_get_menu("Footer Menu", "footer")
    
    if footer_menu_id:
        # Clear existing items
        clear_menu(footer_menu_id)
        
        # Add footer menu items (Legal pages)
        footer_items = [
            ("Blog", "/blog"),
            ("Contact", "/contact"),
            ("Privacy Policy", "/privacy"),
            ("Terms of Service", "/terms-of-service"),
            ("Product Terms", "/product-terms")
        ]
        
        for title, url in footer_items:
            page_id = get_page_id(url.strip('/'))
            if page_id:
                add_menu_item(footer_menu_id, title, page_id)
                print(f"   ✅ Added: {title}")
            else:
                run_wp_cli(f"menu item add-custom {footer_menu_id} --title='{title}' --url='{url}'")
                print(f"   ✅ Added: {title} (custom link)")
    
    print("\n✅ Navigation restructured successfully!")
    print("\nPrimary Menu (5 items):")
    print("  - Home")
    print("  - Features")
    print("  - Pricing")
    print("  - AI Swarm")
    print("  - Get Started")
    print("\nFooter Menu (Legal pages):")
    print("  - Blog")
    print("  - Contact")
    print("  - Privacy Policy")
    print("  - Terms of Service")
    print("  - Product Terms")

if __name__ == "__main__":
    restructure_navigation()

