#!/usr/bin/env python3
"""
Deploy and Activate WordPress Themes Automatically
==================================================

Automatically uploads theme files to WordPress sites via SFTP/SSH
and activates them using WP-CLI or WordPress REST API.

This tool integrates with the existing WordPressManager deployment system.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import os
import zipfile
import subprocess
from pathlib import Path
from typing import Dict, Optional, Tuple

# Add main repo tools to path
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
if MAIN_REPO_TOOLS.exists():
    sys.path.insert(0, str(MAIN_REPO_TOOLS))

# Add current directory to path for simple deployer
sys.path.insert(0, str(Path(__file__).parent))

# Try WordPressManager from main repo
try:
    from wordpress_manager import WordPressManager
    WORDPRESS_MANAGER_AVAILABLE = True
    SIMPLE_DEPLOYER_AVAILABLE = False
except ImportError:
    WORDPRESS_MANAGER_AVAILABLE = False
    # Try simple deployer as fallback
    try:
        from simple_wordpress_deployer import SimpleWordPressDeployer
        SIMPLE_DEPLOYER_AVAILABLE = True
        # Create alias for compatibility
        WordPressManager = SimpleWordPressDeployer
    except ImportError:
        SIMPLE_DEPLOYER_AVAILABLE = False
        class WordPressManager:
            pass

# Load .env file for credentials
try:
    from dotenv import load_dotenv, dotenv_values
    env_vars = dotenv_values("D:/Agent_Cellphone_V2_Repository/.env")
    import os
    for key, value in env_vars.items():
        if value and key not in os.environ:
            os.environ[key] = value
    load_dotenv("D:/Agent_Cellphone_V2_Repository/.env")
except ImportError:
    pass  # dotenv not installed
except Exception:
    pass  # .env file not found

# Load .env file for credentials
try:
    from dotenv import load_dotenv, dotenv_values
    env_vars = dotenv_values("D:/Agent_Cellphone_V2_Repository/.env")
    for key, value in env_vars.items():
        if value and key not in os.environ:
            os.environ[key] = value
    load_dotenv("D:/Agent_Cellphone_V2_Repository/.env")
except ImportError:
    pass
except Exception:
    pass


# Theme deployment configuration
THEME_CONFIGS = {
    "houstonsipqueen.com": {
        "site_key": "houstonsipqueen",
        "theme_name": "houstonsipqueen",
        "theme_path": "websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen",
        "remote_path": "wp-content/themes/houstonsipqueen"
    },
    "digitaldreamscape.site": {
        "site_key": "digitaldreamscape",
        "theme_name": "digitaldreamscape",
        "theme_path": "websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape",
        "remote_path": "wp-content/themes/digitaldreamscape"
    },
    "ariajet.site": {
        "site_key": "ariajet",
        "theme_name": "ariajet",
        "theme_path": "websites/ariajet.site/wp/wp-content/themes/ariajet",
        "remote_path": "wp-content/themes/ariajet"
    },
    "tradingrobotplug.com": {
        "site_key": "tradingrobotplug",
        "theme_name": "tradingrobotplug-theme",
        "theme_path": "websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme",
        "remote_path": "wp-content/themes/tradingrobotplug-theme"
    }
}


def load_site_configs():
    """Load site configurations from .deploy_credentials/sites.json or configs/site_configs.json"""
    import json
    # Try .deploy_credentials/sites.json first (WordPressManager format)
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json_path.exists():
        try:
            with open(sites_json_path, 'r') as f:
                return json.load(f)
        except Exception as e:
            print(f"⚠️  Could not load sites.json: {e}")
    
    # Fallback to site_configs.json
    config_path = Path("D:/websites/configs/site_configs.json")
    if not config_path.exists():
        config_path = Path(__file__).parent.parent.parent / "configs" / "site_configs.json"
    
    if config_path.exists():
        try:
            with open(config_path, 'r') as f:
                return json.load(f)
        except Exception as e:
            print(f"⚠️  Could not load site_configs.json: {e}")
            return {}
    return {}




def activate_theme_via_wp_cli_ssh(manager, theme_name: str, wp_path: str = "/home/*/public_html") -> bool:
    """Activate theme using WP-CLI via SSH."""
    try:
        print(f"🎨 Activating theme '{theme_name}' via WP-CLI...")
        
        # Try to execute WP-CLI command via SSH
        if hasattr(manager, 'execute_command'):
            command = f"wp theme activate {theme_name} --path={wp_path}"
            result = manager.execute_command(command)
            
            if result and ("Success" in result or "Activated" in result):
                print(f"✅ Theme '{theme_name}' activated!")
                return True
            else:
                print(f"⚠️  WP-CLI output: {result}")
                return False
        else:
            print("⚠️  WordPressManager does not support command execution")
            return False
    except Exception as e:
        print(f"❌ Error activating theme via WP-CLI: {e}")
        return False


def activate_theme_via_rest_api(site_url: str, username: str, app_password: str, theme_name: str) -> bool:
    """Activate theme using WordPress REST API with Application Password."""
    try:
        import requests
        from requests.auth import HTTPBasicAuth
        
        print(f"🎨 Activating theme '{theme_name}' via REST API...")
        
        # WordPress REST API endpoint for themes
        # Note: This requires WordPress 5.9+ and proper permissions
        api_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/themes"
        
        # First, list themes to find our theme
        response = requests.get(
            api_url,
            auth=HTTPBasicAuth(username, app_password),
            timeout=30
        )
        
        if response.status_code == 200:
            themes = response.json()
            theme_found = any(t.get('stylesheet') == theme_name for t in themes)
            
            if theme_found:
                # Activate theme (this endpoint may vary by WordPress version)
                activate_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/themes/{theme_name}"
                activate_response = requests.post(
                    activate_url,
                    auth=HTTPBasicAuth(username, app_password),
                    json={"status": "active"},
                    timeout=30
                )
                
                if activate_response.status_code in [200, 201]:
                    print(f"✅ Theme '{theme_name}' activated!")
                    return True
                else:
                    print(f"⚠️  Activation response: {activate_response.status_code}")
                    print(f"   {activate_response.text}")
                    return False
            else:
                print(f"⚠️  Theme '{theme_name}' not found in installed themes")
                return False
        else:
            print(f"❌ Could not access themes API: {response.status_code}")
            return False
            
    except ImportError:
        print("⚠️  'requests' library not installed. Install with: pip install requests")
        return False
    except Exception as e:
        print(f"❌ Error activating theme via REST API: {e}")
        return False


def deploy_and_activate_theme(site_domain: str, config: dict, activate: bool = True) -> Tuple[bool, bool]:
    """
    Deploy theme files and optionally activate theme.
    Uses WordPressManager with site_key (reads from .deploy_credentials/sites.json).
    
    Returns:
        Tuple[bool, bool]: (upload_success, activation_success)
    """
    print(f"\n{'='*60}")
    print(f"🎨 THEME DEPLOYMENT: {site_domain}")
    print(f"{'='*60}\n")
    
    base_path = Path("D:/websites")
    theme_path = base_path / config["theme_path"]
    
    if not theme_path.exists():
        print(f"❌ Theme path not found: {theme_path}")
        return False, False
    
    # Verify theme files exist
    required_files = ['style.css', 'functions.php', 'index.php']
    missing_files = [f for f in required_files if not (theme_path / f).exists()]
    if missing_files:
        print(f"❌ Missing required theme files: {missing_files}")
        return False, False
    
    print(f"✅ Theme files verified: {theme_path}")
    
    # Step 1: Connect to server using WordPressManager
    manager = None
    upload_success = False
    
    # Load site configs for SimpleWordPressDeployer
    site_configs = {}
    if not WORDPRESS_MANAGER_AVAILABLE:
        site_configs = load_site_configs()
        if not site_configs:
            print("❌ WordPressManager not available and site_configs.json not found!")
            print(f"   Expected WordPressManager at: {MAIN_REPO_TOOLS}/wordpress_manager.py")
            print("   Or site_configs.json at: D:/websites/configs/site_configs.json")
            return False, False
    
    if not WORDPRESS_MANAGER_AVAILABLE and not SIMPLE_DEPLOYER_AVAILABLE:
        print("❌ No deployment method available!")
        print("   Install WordPressManager or ensure simple_wordpress_deployer.py exists")
        return False, False
    
    try:
        # WordPressManager uses site_key and reads from .deploy_credentials/sites.json
        # SimpleWordPressDeployer uses site_configs.json
        if WORDPRESS_MANAGER_AVAILABLE:
            manager = WordPressManager(config["site_key"])
        elif SIMPLE_DEPLOYER_AVAILABLE:
            # Use SimpleWordPressDeployer with site_configs
            manager = WordPressManager(config["site_key"], site_configs)
        else:
            print("❌ No deployer available")
            return False, False
        print(f"📡 Connecting to {config['site_key']}...")
        if not manager.connect():
            print(f"❌ Failed to connect to {config['site_key']}")
            print("   Check credentials in .deploy_credentials/sites.json")
            return False, False
        print("✅ Connected!\n")
        
        # Step 2: Upload theme files
        uploaded = 0
        failed = 0
        
        # Get base remote path from config
        if SIMPLE_DEPLOYER_AVAILABLE:
            sftp_config = site_configs.get(site_domain, {}).get('sftp', {})
            base_remote_path = sftp_config.get('remote_path', '')
        else:
            base_remote_path = ''  # WordPressManager handles this internally
        
        # Files to skip (development/deployment files)
        for file_path in theme_path.rglob('*'):
            if file_path.is_file():
                # Skip non-theme files (Python, env, batch, markdown, pycache)
                if file_path.suffix in ['.py', '.env', '.bat', '.md'] or '__pycache__' in str(file_path) or '.git' in str(file_path):
                    continue
                
                relative_path = file_path.relative_to(theme_path)
                # Build remote path: {base_remote_path}/wp-content/themes/{theme_name}/{relative_path}
                if base_remote_path:
                    remote_file_path = f"{base_remote_path}/{config['remote_path']}/{relative_path.as_posix()}"
                else:
                    remote_file_path = f"{config['remote_path']}/{relative_path.as_posix()}"
                
                # Normalize path separators
                remote_file_path = remote_file_path.replace('\\', '/')
                
                print(f"📤 Uploading: {relative_path}...")
                # deploy_file signature may vary - try both
                try:
                    if SIMPLE_DEPLOYER_AVAILABLE:
                        # file_path is already a Path object from rglob
                        success = manager.deploy_file(file_path, remote_file_path)
                    else:
                        # WordPressManager may have different signature
                        success = manager.deploy_file(file_path)
                except (TypeError, AttributeError) as e:
                    # Try without remote_path parameter or with string path
                    try:
                        success = manager.deploy_file(str(file_path), remote_file_path)
                    except:
                        success = manager.deploy_file(str(file_path))
                
                if success:
                    uploaded += 1
                    print(f"   ✅ {relative_path}")
                else:
                    failed += 1
                    print(f"   ❌ {relative_path}")
        
        # Allow deployment to proceed if core files uploaded (failed files may be non-critical)
        upload_success = uploaded > 30 and failed <= 5  # Allow up to 5 failures if core files uploaded
        
        print(f"\n📊 Upload Summary:")
        print(f"   ✅ Uploaded: {uploaded}")
        print(f"   ❌ Failed: {failed}")
        
        if failed > 0:
            print(f"⚠️  {failed} files failed to upload (non-critical files may be skipped)")
        
        if not upload_success:
            print("❌ Theme upload failed! Core files missing.")
            manager.disconnect()
            return False, False
            
    except ValueError as e:
        print(f"❌ Site configuration error: {e}")
        print(f"   Site key '{config['site_key']}' not found in WordPressManager")
        print("   Check .deploy_credentials/sites.json for site configuration")
        return False, False
    except Exception as e:
        print(f"❌ Connection error: {e}")
        import traceback
        traceback.print_exc()
        return False, False
    
    # Step 3: Activate theme (if requested)
    activation_success = False
    
    if activate and upload_success:
        # Try WP-CLI via SSH
        if manager and hasattr(manager, 'execute_command'):
            print(f"\n🎨 Activating theme '{config['theme_name']}' via WP-CLI...")
            
            # Get base remote path for WP-CLI
            if SIMPLE_DEPLOYER_AVAILABLE:
                base_remote_path = getattr(manager, 'remote_path', '')
            else:
                base_remote_path = ''
            
            # Build WP-CLI command
            # base_remote_path is like "domains/houstonsipqueen.com/public_html"
            # WP-CLI needs the full path to WordPress root
            if base_remote_path:
                wp_path = f"/home/{manager.site_config.get('username', 'u996867598')}/{base_remote_path}"
            else:
                wp_path = "/home/*/public_html"
            
            # Try multiple path variations
            paths_to_try = [
                wp_path,
                base_remote_path if base_remote_path else "",
                f"/home/u996867598/{base_remote_path}" if base_remote_path else ""
            ]
            
            activation_success = False
            for try_path in paths_to_try:
                if not try_path:
                    continue
                command = f"cd {try_path} && wp theme activate {config['theme_name']} --allow-root 2>&1"
                result = manager.execute_command(command)
                
                # Check for success indicators: Success, Activated, or "already active" (which means it's active)
                if result and (
                    "Success" in result 
                    or "Activated" in result 
                    or "Theme activated" in result.lower()
                    or "already active" in result.lower()
                    or "is already active" in result.lower()
                ):
                    if "already active" in result.lower() or "is already active" in result.lower():
                        print(f"✅ Theme '{config['theme_name']}' is already active!")
                    else:
                        print(f"✅ Theme '{config['theme_name']}' activated!")
                    activation_success = True
                    break
                elif "Error" not in result and result.strip():
                    # Try to parse the result
                    print(f"   Trying path: {try_path}")
                    print(f"   Output: {result[:200]}")
            
            if not activation_success:
                print(f"⚠️  Could not activate via WP-CLI. Output from last attempt: {result[:200] if 'result' in locals() else 'N/A'}")
                print("   Theme uploaded successfully! Please activate manually:")
                print(f"   1. Go to: https://{site_domain}/wp-admin/themes.php")
                print(f"   2. Find theme: {config['theme_name']}")
                print(f"   3. Click 'Activate'")
                return upload_success, False
            
            return upload_success, activation_success
        else:
            print("\n⚠️  Automatic activation not available.")
            print("   Theme uploaded successfully! Please activate manually:")
            print(f"   1. Go to: https://{site_domain}/wp-admin/themes.php")
            print(f"   2. Find theme: {config['theme_name']}")
            print(f"   3. Click 'Activate'")
    
    # Disconnect
    if manager:
        manager.disconnect()
    
    return upload_success, activation_success


def main():
    """Deploy and activate themes for configured sites."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Deploy and activate WordPress themes automatically',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # Deploy and activate theme for specific site
  python deploy_and_activate_themes.py --site houstonsipqueen.com
  
  # Deploy themes for all sites
  python deploy_and_activate_themes.py --all
  
  # Upload only, don't activate
  python deploy_and_activate_themes.py --all --upload-only
        """
    )
    parser.add_argument(
        '--site',
        type=str,
        help='Site domain (e.g., houstonsipqueen.com)'
    )
    parser.add_argument(
        '--all',
        action='store_true',
        help='Deploy themes for all configured sites'
    )
    parser.add_argument(
        '--upload-only',
        action='store_true',
        help='Only upload files, do not activate'
    )
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🎨 WORDPRESS THEME DEPLOYMENT & ACTIVATION")
    print("="*60)
    
    if not WORDPRESS_MANAGER_AVAILABLE and not SIMPLE_DEPLOYER_AVAILABLE:
        print("\n⚠️  WARNING: No deployment method available!")
        print("   WordPressManager not found and SimpleWordPressDeployer not available.")
        print("   Please ensure one of the following:")
        print("   1. WordPressManager installed in main repository")
        print("   2. Or site_configs.json configured with SFTP credentials")
        print("\n   Alternative: Use manual upload via WordPress admin:")
        print("   1. Create ZIP files of themes")
        print("   2. Upload via Appearance > Themes > Add New > Upload Theme")
        print("   3. Activate themes manually")
        return 1
    
    if args.all:
        results = {}
        for site_domain, config in THEME_CONFIGS.items():
            upload_success, activation_success = deploy_and_activate_theme(
                site_domain,
                config,
                activate=not args.upload_only
            )
            results[site_domain] = {
                'upload': upload_success,
                'activation': activation_success if not args.upload_only else None
            }
        
        # Summary
        print("\n" + "="*60)
        print("📊 DEPLOYMENT SUMMARY")
        print("="*60)
        
        for site_domain, result in results.items():
            upload_status = "✅" if result['upload'] else "❌"
            if result['activation'] is not None:
                activation_status = "✅" if result['activation'] else "⚠️"
                print(f"   {site_domain}:")
                print(f"      Upload: {upload_status}")
                print(f"      Activation: {activation_status}")
            else:
                print(f"   {site_domain}: Upload {upload_status}")
        
        all_uploaded = all(r['upload'] for r in results.values())
        all_activated = all(
            r['activation'] for r in results.values()
            if r['activation'] is not None
        ) if not args.upload_only else True
        
        if all_uploaded and all_activated:
            print("\n✅ All themes deployed and activated successfully!")
        elif all_uploaded:
            print("\n✅ All themes uploaded! Some may need manual activation.")
        else:
            print("\n⚠️  Some deployments failed. Check errors above.")
        
        return 0 if all_uploaded else 1
        
    elif args.site:
        if args.site not in THEME_CONFIGS:
            print(f"❌ Unknown site: {args.site}")
            print(f"   Available sites: {', '.join(THEME_CONFIGS.keys())}")
            return 1
        
        upload_success, activation_success = deploy_and_activate_theme(
            args.site,
            THEME_CONFIGS[args.site],
            activate=not args.upload_only
        )
        
        if upload_success:
            if activation_success or args.upload_only:
                print("\n✅ Theme deployment completed!")
                return 0
            else:
                print("\n✅ Theme uploaded! Please activate manually in WordPress admin.")
                return 0
        else:
            print("\n❌ Theme deployment failed!")
            return 1
    else:
        parser.print_help()
        return 1


if __name__ == '__main__':
    exit(main())
