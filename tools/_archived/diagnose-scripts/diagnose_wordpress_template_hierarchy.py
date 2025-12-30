#!/usr/bin/env python3
"""
WordPress Template Hierarchy Diagnostic Tool

Diagnoses WordPress template loading issues by checking:
- show_on_front setting (posts vs page)
- Template files present in theme
- Template hierarchy conflicts
- Active theme and template selection
- Suggests template override solutions

Created: 2025-12-26
Agent: Agent-7
"""

import os
import sys
import json
import paramiko
from pathlib import Path
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

def get_site_config(site_domain):
    """Load site configuration from sites.json or .env"""
    # Try sites.json first
    sites_json_path = Path(__file__).parent.parent / "sites.json"
    if sites_json_path.exists():
        with open(sites_json_path, 'r') as f:
            sites = json.load(f)
            if site_domain in sites:
                return sites[site_domain]
    
    # Fall back to .env
    return {
        "host": os.getenv("HOSTINGER_HOST"),
        "username": os.getenv("HOSTINGER_USER"),
        "password": os.getenv("HOSTINGER_PASS"),
        "port": int(os.getenv("HOSTINGER_PORT", "65002"))
    }

def check_wordpress_settings(site_domain):
    """Check WordPress show_on_front and page_for_posts settings"""
    config = get_site_config(site_domain)
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(config['host'], port=config['port'], 
                   username=config['username'], password=config['password'])
        
        wp_path = f"/home/{config['username']}/domains/{site_domain}/public_html"
        
        # Get show_on_front setting
        stdin, stdout, stderr = ssh.exec_command(
            f"cd {wp_path} && wp option get show_on_front --allow-root"
        )
        show_on_front = stdout.read().decode().strip()
        
        # Get page_for_posts if show_on_front is 'page'
        page_for_posts = None
        if show_on_front == 'page':
            stdin, stdout, stderr = ssh.exec_command(
                f"cd {wp_path} && wp option get page_for_posts --allow-root"
            )
            page_for_posts = stdout.read().decode().strip()
        
        # Get page_on_front if show_on_front is 'page'
        page_on_front = None
        if show_on_front == 'page':
            stdin, stdout, stderr = ssh.exec_command(
                f"cd {wp_path} && wp option get page_on_front --allow-root"
            )
            page_on_front = stdout.read().decode().strip()
        
        ssh.close()
        
        return {
            'show_on_front': show_on_front,
            'page_for_posts': page_for_posts,
            'page_on_front': page_on_front
        }
    except Exception as e:
        print(f"❌ Error checking WordPress settings: {e}")
        return None

def check_theme_templates(site_domain, theme_name):
    """Check which template files exist in the theme"""
    config = get_site_config(site_domain)
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(config['host'], port=config['port'], 
                   username=config['username'], password=config['password'])
        
        theme_path = f"/home/{config['username']}/domains/{site_domain}/public_html/wp-content/themes/{theme_name}"
        
        # Check for common template files
        templates = {
            'front-page.php': False,
            'home.php': False,
            'index.php': False,
            'page.php': False,
            'single.php': False,
            'archive.php': False,
        }
        
        for template in templates.keys():
            stdin, stdout, stderr = ssh.exec_command(f"test -f {theme_path}/{template} && echo 'exists' || echo 'missing'")
            result = stdout.read().decode().strip()
            templates[template] = result == 'exists'
        
        ssh.close()
        
        return templates
    except Exception as e:
        print(f"❌ Error checking theme templates: {e}")
        return None

def check_active_theme(site_domain):
    """Get the active theme name"""
    config = get_site_config(site_domain)
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(config['host'], port=config['port'], 
                   username=config['username'], password=config['password'])
        
        wp_path = f"/home/{config['username']}/domains/{site_domain}/public_html"
        
        stdin, stdout, stderr = ssh.exec_command(
            f"cd {wp_path} && wp theme list --status=active --field=name --allow-root"
        )
        theme_name = stdout.read().decode().strip()
        
        ssh.close()
        
        return theme_name
    except Exception as e:
        print(f"❌ Error checking active theme: {e}")
        return None

def diagnose_template_hierarchy(site_domain):
    """Main diagnostic function"""
    print(f"\n{'='*60}")
    print(f"WordPress Template Hierarchy Diagnostic")
    print(f"Site: {site_domain}")
    print(f"{'='*60}\n")
    
    # Get active theme
    print("📋 Checking active theme...")
    theme_name = check_active_theme(site_domain)
    if not theme_name:
        print("❌ Could not determine active theme")
        return
    print(f"✅ Active theme: {theme_name}\n")
    
    # Check WordPress settings
    print("📋 Checking WordPress settings...")
    settings = check_wordpress_settings(site_domain)
    if not settings:
        print("❌ Could not check WordPress settings")
        return
    
    print(f"✅ show_on_front: {settings['show_on_front']}")
    if settings['page_for_posts']:
        print(f"✅ page_for_posts: {settings['page_for_posts']}")
    if settings['page_on_front']:
        print(f"✅ page_on_front: {settings['page_on_front']}")
    print()
    
    # Check template files
    print("📋 Checking theme template files...")
    templates = check_theme_templates(site_domain, theme_name)
    if not templates:
        print("❌ Could not check template files")
        return
    
    for template, exists in templates.items():
        status = "✅" if exists else "❌"
        print(f"{status} {template}: {'EXISTS' if exists else 'MISSING'}")
    print()
    
    # Diagnose issues
    print("🔍 DIAGNOSIS:")
    print("-" * 60)
    
    issues = []
    suggestions = []
    
    # Check front-page.php loading
    if settings['show_on_front'] == 'posts':
        if templates['front-page.php']:
            issues.append("⚠️  WordPress is set to show posts on front, but front-page.php exists")
            issues.append("   WordPress will use index.php or home.php instead of front-page.php")
            suggestions.append("SOLUTION: Add template_include filter to force front-page.php:")
            suggestions.append("  function force_front_page_template($template) {")
            suggestions.append("    if (is_front_page() && is_home()) {")
            suggestions.append("      $front = locate_template('front-page.php');")
            suggestions.append("      if ($front) return $front;")
            suggestions.append("    }")
            suggestions.append("    return $template;")
            suggestions.append("  }")
            suggestions.append("  add_filter('template_include', 'force_front_page_template', 99);")
        else:
            issues.append("ℹ️  WordPress is set to show posts on front, and no front-page.php exists")
            issues.append("   WordPress will use index.php or home.php (expected behavior)")
    
    if settings['show_on_front'] == 'page':
        if settings['page_on_front'] == '0':
            issues.append("⚠️  WordPress is set to show a static page, but no page selected")
            suggestions.append("SOLUTION: Set page_on_front option or change show_on_front to 'posts'")
    
    # Check template file priorities
    if templates['front-page.php'] and templates['home.php']:
        issues.append("ℹ️  Both front-page.php and home.php exist")
        if settings['show_on_front'] == 'posts':
            issues.append("   When showing posts, home.php takes priority over front-page.php")
    
    if not templates['index.php']:
        issues.append("❌ CRITICAL: index.php is missing (required fallback template)")
        suggestions.append("SOLUTION: Create index.php as fallback template")
    
    if issues:
        for issue in issues:
            print(issue)
        print()
    
    if suggestions:
        print("💡 SUGGESTIONS:")
        print("-" * 60)
        for suggestion in suggestions:
            print(suggestion)
        print()
    
    # Expected template for homepage
    print("📄 Expected Template for Homepage:")
    print("-" * 60)
    if settings['show_on_front'] == 'posts':
        if templates['home.php']:
            print("✅ WordPress will use: home.php")
        elif templates['front-page.php']:
            print("⚠️  WordPress will use: index.php (front-page.php ignored when showing posts)")
        else:
            print("✅ WordPress will use: index.php")
    else:
        if templates['front-page.php']:
            print("✅ WordPress will use: front-page.php")
        elif templates['page.php']:
            print("✅ WordPress will use: page.php")
        else:
            print("✅ WordPress will use: index.php")
    print()
    
    print("=" * 60)

def main():
    """CLI entry point"""
    if len(sys.argv) < 2:
        print("Usage: python diagnose_wordpress_template_hierarchy.py <site_domain>")
        print("Example: python diagnose_wordpress_template_hierarchy.py dadudekc.com")
        sys.exit(1)
    
    site_domain = sys.argv[1]
    diagnose_template_hierarchy(site_domain)

if __name__ == "__main__":
    main()

