#!/usr/bin/env python3
"""
Activate Custom Themes for Websites
Activates custom themes on specified WordPress sites

Usage:
    python ops/deployment/activate_themes.py --site houstonsipqueen.com
    python ops/deployment/activate_themes.py --site digitaldreamscape.site
    python ops/deployment/activate_themes.py --all
"""

import argparse
import sys
import os
from pathlib import Path

# Add parent directory to path for imports
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

def activate_theme_via_wp_cli(site_domain, theme_name, wp_path=None):
    """
    Activate theme using WP-CLI
    
    Args:
        site_domain: Domain name (e.g., 'houstonsipqueen.com')
        theme_name: Theme directory name (e.g., 'houstonsipqueen')
        wp_path: Path to WordPress installation (optional)
    
    Returns:
        bool: True if successful, False otherwise
    """
    # Try to use WordPressManager for remote WP-CLI execution
    try:
        from wordpress_manager import WordPressManager
        
        # Map domain to site_key
        site_key_map = {
            'houstonsipqueen.com': 'houstonsipqueen',
            'digitaldreamscape.site': 'digitaldreamscape'
        }
        
        site_key = site_key_map.get(site_domain)
        if not site_key:
            print(f"⚠️  No site_key mapping for {site_domain}")
            return False
        
        manager = WordPressManager(site_key)
        if manager.connect():
            print(f"📦 Activating theme '{theme_name}' for {site_domain} via SSH...")
            
            # Execute WP-CLI command via SSH
            command = f"wp theme activate {theme_name} --path=/home/*/public_html"
            if hasattr(manager, 'execute_command'):
                result = manager.execute_command(command)
                manager.disconnect()
                
                if result and ("Success" in result or "Activated" in result):
                    print(f"✅ Theme '{theme_name}' activated successfully!")
                    return True
                else:
                    print(f"⚠️  WP-CLI output: {result}")
                    return False
            else:
                manager.disconnect()
                print("⚠️  WordPressManager does not support command execution")
                return False
        else:
            print(f"❌ Failed to connect to {site_key}")
            return False
    except ImportError:
        print("⚠️  WordPressManager not available")
    except Exception as e:
        print(f"❌ Error with WordPressManager: {e}")
    
    # Fallback to local WP-CLI
    if wp_path is None:
        # Try to find WordPress path
        repo_root = Path(__file__).parent.parent.parent
        wp_path = repo_root / 'websites' / site_domain / 'wp'
        
        if not wp_path.exists():
            print(f"❌ WordPress path not found: {wp_path}")
            return False
    
    wp_cli_cmd = f'wp theme activate {theme_name} --path={wp_path}'
    
    print(f"📦 Activating theme '{theme_name}' for {site_domain}...")
    print(f"   Command: {wp_cli_cmd}")
    
    # Try to execute WP-CLI command
    import subprocess
    try:
        result = subprocess.run(
            wp_cli_cmd.split(),
            cwd=str(wp_path),
            capture_output=True,
            text=True,
            timeout=30
        )
        
        if result.returncode == 0:
            print(f"✅ Theme '{theme_name}' activated successfully!")
            return True
        else:
            print(f"❌ WP-CLI error: {result.stderr}")
            return False
    except FileNotFoundError:
        print("⚠️  WP-CLI not found. Trying alternative method...")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def activate_theme_via_rest_api(site_domain, theme_name):
    """
    Activate theme using WordPress REST API (requires authentication)
    
    Note: This requires admin credentials and may not be available
    """
    print(f"⚠️  REST API activation not implemented (requires authentication)")
    print(f"   Please activate theme manually in WordPress admin:")
    print(f"   1. Go to Appearance > Themes")
    print(f"   2. Find '{theme_name}' theme")
    print(f"   3. Click 'Activate'")
    return False

def generate_activation_instructions(site_domain, theme_name):
    """
    Generate manual activation instructions
    """
    instructions = f"""
# Theme Activation Instructions for {site_domain}

## Theme: {theme_name}

### Method 1: WordPress Admin (Recommended)
1. Log into WordPress admin: https://{site_domain}/wp-admin
2. Navigate to: **Appearance > Themes**
3. Find the theme: **{theme_name}**
4. Click **"Activate"** button
5. Verify the theme is active

### Method 2: WP-CLI (If available)
```bash
cd /path/to/{site_domain}/wp
wp theme activate {theme_name}
```

### Method 3: Database (Advanced)
If you have database access, you can update the option directly:
```sql
UPDATE wp_options 
SET option_value = '{theme_name}' 
WHERE option_name = 'template' OR option_name = 'stylesheet';
```

### Verification
After activation, visit https://{site_domain} and verify:
- Theme styling is applied
- Custom header/footer are visible
- Navigation works correctly
- Forms are functional

### Theme Location
Theme files should be at:
`websites/{site_domain}/wp/wp-content/themes/{theme_name}/`

### Troubleshooting
- If theme doesn't appear: Check that theme files are uploaded to server
- If activation fails: Check file permissions
- If styling breaks: Clear cache and check for plugin conflicts
"""
    return instructions

def main():
    parser = argparse.ArgumentParser(
        description='Activate custom themes on WordPress sites'
    )
    parser.add_argument(
        '--site',
        type=str,
        help='Site domain (e.g., houstonsipqueen.com)'
    )
    parser.add_argument(
        '--theme',
        type=str,
        help='Theme directory name (defaults to site name)'
    )
    parser.add_argument(
        '--all',
        action='store_true',
        help='Activate themes for all sites that need it'
    )
    parser.add_argument(
        '--instructions',
        action='store_true',
        help='Generate activation instructions instead of attempting activation'
    )
    
    args = parser.parse_args()
    
    # Sites that need theme activation
    sites_to_activate = {
        'houstonsipqueen.com': 'houstonsipqueen',
        'digitaldreamscape.site': 'digitaldreamscape'
    }
    
    if args.instructions:
        # Generate instructions for all sites
        for site, theme in sites_to_activate.items():
            instructions = generate_activation_instructions(site, theme)
            output_file = Path(__file__).parent / f'{site.replace(".", "_")}_activation_instructions.md'
            output_file.write_text(instructions)
            print(f"✅ Generated instructions: {output_file}")
        return
    
    if args.all:
        # Activate themes for all sites
        success_count = 0
        for site, theme in sites_to_activate.items():
            print(f"\n{'='*60}")
            print(f"Processing: {site}")
            print(f"{'='*60}")
            
            if activate_theme_via_wp_cli(site, theme):
                success_count += 1
            else:
                # Generate instructions as fallback
                instructions = generate_activation_instructions(site, theme)
                output_file = Path(__file__).parent / f'{site.replace(".", "_")}_activation_instructions.md'
                output_file.write_text(instructions)
                print(f"📄 Manual instructions saved to: {output_file}")
        
        print(f"\n{'='*60}")
        print(f"Summary: {success_count}/{len(sites_to_activate)} themes activated")
        print(f"{'='*60}")
        
    elif args.site:
        theme = args.theme or sites_to_activate.get(args.site)
        if not theme:
            print(f"❌ Unknown site: {args.site}")
            print(f"   Available sites: {', '.join(sites_to_activate.keys())}")
            return
        
        if activate_theme_via_wp_cli(args.site, theme):
            print("✅ Theme activation complete!")
        else:
            # Generate instructions as fallback
            instructions = generate_activation_instructions(args.site, theme)
            output_file = Path(__file__).parent / f'{args.site.replace(".", "_")}_activation_instructions.md'
            output_file.write_text(instructions)
            print(f"\n📄 Manual activation required. Instructions saved to: {output_file}")
            print("\n" + instructions)
    else:
        parser.print_help()
        print("\n💡 Tip: Use --instructions to generate manual activation guides")

if __name__ == '__main__':
    main()

