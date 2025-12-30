#!/usr/bin/env python3
"""
Generic Template Deployer
==========================

Deploys page templates and CSS to any WordPress site using unified_wordpress_manager.
Works with all sites in site_configs.json.

Usage:
    python tools/generic_deploy_template.py --site example.com --template page-blog.php --local-path themes/theme-name/page-templates/page-blog.php
    python tools/generic_deploy_template.py --site example.com --template page-blog.php --css themes/theme-name/assets/css/blog.css
    python tools/generic_deploy_template.py --site example.com --template page-blog.php --local-path template.php --css styles.css --theme-name my-theme

Agent-7: Web Development Specialist
"""

import argparse
import sys
from pathlib import Path
from datetime import datetime

sys.path.insert(0, str(Path(__file__).parent))
from unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod


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


def deploy_template_file(manager: UnifiedWordPressManager, local_file: Path, remote_path: str) -> bool:
    """Deploy template file via SFTP."""
    if not manager.deployer:
        return False
    
    if not manager.deployer.connect():
        return False
    
    try:
        if not local_file.exists():
            print(f"❌ Local file not found: {local_file}")
            return False
        
        with open(local_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Create backup if file exists
        try:
            manager.deployer.sftp.stat(remote_path)
            backup_path = f"{remote_path}.backup_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
            with manager.deployer.sftp.open(remote_path, 'r') as src:
                with manager.deployer.sftp.open(backup_path, 'w') as dst:
                    dst.write(src.read())
            print(f"💾 Backup created: {backup_path}")
        except:
            pass
        
        # Deploy
        with manager.deployer.sftp.open(remote_path, 'w') as f:
            f.write(content.encode('utf-8'))
        
        return True
    except Exception as e:
        print(f"❌ Deployment error: {e}")
        return False
    finally:
        manager.deployer.disconnect()


def deploy_css_file(manager: UnifiedWordPressManager, local_file: Path, remote_path: str) -> bool:
    """Deploy CSS file via SFTP."""
    return deploy_template_file(manager, local_file, remote_path)


def add_css_enqueue(manager: UnifiedWordPressManager, css_path: str, handle: str) -> bool:
    """Add CSS enqueue to functions.php."""
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
        
        # Check if enqueue already exists
        if handle in functions_content or css_path in functions_content:
            print(f"ℹ️  CSS enqueue already exists")
            return True
        
        # Add enqueue function
        enqueue_code = f"""
// Enqueue {handle} CSS
function enqueue_{handle.replace('-', '_')}_styles() {{
    if (is_page_template('{css_path.split('/')[-1].replace('.css', '.php')}')) {{
        wp_enqueue_style(
            '{handle}',
            get_template_directory_uri() . '/{css_path}',
            array(),
            '1.0.0'
        );
    }}
}}
add_action('wp_enqueue_scripts', 'enqueue_{handle.replace('-', '_')}_styles');
"""
        
        # Append to functions.php
        new_content = functions_content.rstrip() + "\n" + enqueue_code
        
        with manager.deployer.sftp.open(functions_path, 'w') as f:
            f.write(new_content.encode('utf-8'))
        
        return True
    except Exception as e:
        print(f"⚠️  Could not update functions.php: {e}")
        return False
    finally:
        manager.deployer.disconnect()


def main():
    """Main execution."""
    parser = argparse.ArgumentParser(description='Deploy templates and CSS to any WordPress site')
    parser.add_argument('--site', required=True, help='Site domain (e.g., example.com)')
    parser.add_argument('--template', required=True, help='Template filename (e.g., page-blog.php)')
    parser.add_argument('--local-path', required=True, help='Local path to template file')
    parser.add_argument('--css', help='Local path to CSS file (optional)')
    parser.add_argument('--css-remote-path', help='Remote CSS path relative to theme (e.g., assets/css/blog.css)')
    parser.add_argument('--theme-name', help='Theme name (auto-detected if not provided)')
    parser.add_argument('--template-dir', default='page-templates', help='Template directory (default: page-templates)')
    
    args = parser.parse_args()
    
    print("=" * 70)
    print(f"DEPLOY TEMPLATE - {args.site}")
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
    
    # Get theme name
    if args.theme_name:
        theme_name = args.theme_name
    else:
        print("🔍 Detecting active theme...")
        theme_name = get_theme_name(manager)
        if not theme_name:
            print("❌ Could not determine theme name")
            return
        print(f"✅ Active theme: {theme_name}")
    
    print()
    
    # Deploy template
    local_template = Path(args.local_path)
    if not local_template.exists():
        print(f"❌ Template file not found: {local_template}")
        return
    
    remote_path = getattr(manager.deployer, 'remote_path', '')
    if not remote_path:
        remote_path = f"domains/{args.site}/public_html"
    
    remote_template_path = f"{remote_path}/wp-content/themes/{theme_name}/{args.template_dir}/{args.template}"
    
    print(f"📝 Deploying template: {args.template}")
    print(f"   Local: {local_template}")
    print(f"   Remote: {remote_template_path}")
    print()
    
    # Create template directory if needed
    if not manager.deployer:
        print("❌ Deployer not available")
        return
    
    if not manager.deployer.connect():
        print("❌ Failed to connect")
        return
    
    try:
        # Create directories
        template_dir = f"{remote_path}/wp-content/themes/{theme_name}/{args.template_dir}"
        try:
            manager.deployer.sftp.mkdir(template_dir)
        except:
            pass
        
        # Deploy template
        if deploy_template_file(manager, local_template, remote_template_path):
            print("✅ Template deployed")
        else:
            print("❌ Failed to deploy template")
            return
        
        # Deploy CSS if provided
        if args.css:
            local_css = Path(args.css)
            if not local_css.exists():
                print(f"⚠️  CSS file not found: {local_css}")
            else:
                css_remote = args.css_remote_path or f"assets/css/{args.template.replace('.php', '.css')}"
                remote_css_path = f"{remote_path}/wp-content/themes/{theme_name}/{css_remote}"
                
                # Create CSS directory if needed
                css_dir = f"{remote_path}/wp-content/themes/{theme_name}/assets/css"
                try:
                    manager.deployer.sftp.mkdir(f"{remote_path}/wp-content/themes/{theme_name}/assets")
                    manager.deployer.sftp.mkdir(css_dir)
                except:
                    pass
                
                print()
                print(f"🎨 Deploying CSS: {css_remote}")
                if deploy_css_file(manager, local_css, remote_css_path):
                    print("✅ CSS deployed")
                    
                    # Add enqueue to functions.php
                    print()
                    print("📝 Adding CSS enqueue to functions.php...")
                    css_handle = args.template.replace('.php', '').replace('-', '_')
                    if add_css_enqueue(manager, css_remote, css_handle):
                        print("✅ CSS enqueue added")
        
    finally:
        manager.deployer.disconnect()
    
    print()
    print("=" * 70)
    print("✅ DEPLOYMENT COMPLETE")
    print("=" * 70)
    print()
    print(f"Template available at:")
    print(f"  {remote_template_path}")
    print()
    print("To activate:")
    print(f"  wp post update <page_id> --page_template={args.template_dir}/{args.template}")

if __name__ == "__main__":
    main()

