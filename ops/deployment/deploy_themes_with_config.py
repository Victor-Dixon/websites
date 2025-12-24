#!/usr/bin/env python3
"""
Deploy Themes Using site_configs.json
=====================================

Deploys WordPress themes using credentials from configs/site_configs.json.
Works with SFTP directly, no WordPressManager required.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import json
from pathlib import Path
from typing import Dict, Optional, Tuple

# Theme deployment configuration
THEME_CONFIGS = {
    "houstonsipqueen.com": {
        "theme_name": "houstonsipqueen",
        "theme_path": "websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen",
        "remote_path": "wp-content/themes/houstonsipqueen"
    },
    "digitaldreamscape.site": {
        "theme_name": "digitaldreamscape",
        "theme_path": "websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape",
        "remote_path": "wp-content/themes/digitaldreamscape"
    }
}


def load_site_configs():
    """Load site configurations from configs/site_configs.json"""
    config_path = Path("D:/websites/configs/site_configs.json")
    if not config_path.exists():
        config_path = Path(__file__).parent.parent.parent / "configs" / "site_configs.json"
    
    if config_path.exists():
        try:
            with open(config_path, 'r') as f:
                return json.load(f)
        except Exception as e:
            print(f"❌ Could not load site_configs.json: {e}")
            return {}
    else:
        print(f"❌ site_configs.json not found at: {config_path}")
    return {}


def deploy_via_sftp(host, username, password, port, theme_path, remote_base_path, remote_theme_path):
    """Deploy theme files via SFTP using paramiko."""
    try:
        import paramiko
        
        print(f"📡 Connecting to {host}:{port}...")
        transport = paramiko.Transport((host, port))
        transport.connect(username=username, password=password)
        sftp = paramiko.SFTPClient.from_transport(transport)
        
        print(f"✅ Connected! Uploading theme files...")
        
        uploaded = 0
        failed = 0
        
        # Build full remote path
        full_remote_base = f"{remote_base_path}/{remote_theme_path}"
        
        # Upload each file
        for file_path in theme_path.rglob('*'):
            if file_path.is_file():
                relative_path = file_path.relative_to(theme_path)
                remote_file = f"{full_remote_base}/{relative_path.as_posix()}"
                
                # Ensure remote directory exists
                remote_dir = str(Path(remote_file).parent)
                try:
                    sftp.stat(remote_dir)
                except FileNotFoundError:
                    # Create directory recursively
                    parts = remote_dir.strip('/').split('/')
                    current_path = ''
                    for part in parts:
                        if part:
                            current_path = f"{current_path}/{part}" if current_path else f"/{part}"
                            try:
                                sftp.stat(current_path)
                            except FileNotFoundError:
                                sftp.mkdir(current_path)
                
                # Upload file
                try:
                    sftp.put(str(file_path), remote_file)
                    uploaded += 1
                    print(f"   ✅ {relative_path}")
                except Exception as e:
                    failed += 1
                    print(f"   ❌ {relative_path}: {e}")
        
        sftp.close()
        transport.close()
        
        print(f"\n📊 Upload Summary:")
        print(f"   ✅ Uploaded: {uploaded}")
        print(f"   ❌ Failed: {failed}")
        
        return failed == 0
        
    except ImportError:
        print("❌ paramiko library not installed")
        print("   Install with: pip install paramiko")
        return False
    except Exception as e:
        print(f"❌ SFTP error: {e}")
        return False


def activate_via_wp_cli_ssh(host, username, password, port, theme_name, remote_base_path):
    """Activate theme via WP-CLI over SSH."""
    try:
        import paramiko
        
        print(f"🎨 Activating theme '{theme_name}' via SSH...")
        
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        # Execute WP-CLI command
        wp_path = f"{remote_base_path}"
        command = f"cd {wp_path} && wp theme activate {theme_name}"
        stdin, stdout, stderr = ssh.exec_command(command)
        
        output = stdout.read().decode()
        error = stderr.read().decode()
        
        ssh.close()
        
        if "Success" in output or "Activated" in output or not error:
            print(f"✅ Theme '{theme_name}' activated!")
            return True
        else:
            print(f"⚠️  Activation output: {output}")
            if error:
                print(f"   Error: {error}")
            return False
            
    except ImportError:
        print("❌ paramiko library not installed")
        return False
    except Exception as e:
        print(f"❌ SSH error: {e}")
        return False


def deploy_theme(site_domain: str, theme_config: dict, site_configs: dict, activate: bool = True) -> Tuple[bool, bool]:
    """Deploy theme using site_configs.json credentials."""
    print(f"\n{'='*60}")
    print(f"🎨 THEME DEPLOYMENT: {site_domain}")
    print(f"{'='*60}\n")
    
    base_path = Path("D:/websites")
    theme_path = base_path / theme_config["theme_path"]
    
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
    
    # Get site configuration
    site_config = site_configs.get(site_domain, {})
    if not site_config:
        print(f"❌ No configuration found for {site_domain} in site_configs.json")
        return False, False
    
    # Get SFTP credentials
    sftp_config = site_config.get('sftp', {})
    host = sftp_config.get('host')
    username = sftp_config.get('username')
    password = sftp_config.get('password')
    remote_base_path = sftp_config.get('remote_path', '')
    
    if not all([host, username, password]):
        print(f"❌ Incomplete SFTP credentials for {site_domain}")
        print("   Please add SFTP credentials to configs/site_configs.json")
        return False, False
    
    port = 22  # Default SFTP port
    
    # Upload files
    upload_success = deploy_via_sftp(
        host, username, password, port,
        theme_path, remote_base_path, theme_config["remote_path"]
    )
    
    if not upload_success:
        return False, False
    
    # Activate theme
    activation_success = False
    if activate:
        activation_success = activate_via_wp_cli_ssh(
            host, username, password, port,
            theme_config["theme_name"], remote_base_path
        )
    
    return upload_success, activation_success


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Deploy WordPress themes using site_configs.json'
    )
    parser.add_argument('--site', type=str, help='Site domain')
    parser.add_argument('--all', action='store_true', help='Deploy all themes')
    parser.add_argument('--upload-only', action='store_true', help='Upload only, don\'t activate')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🎨 WORDPRESS THEME DEPLOYMENT (site_configs.json)")
    print("="*60)
    
    # Load site configurations
    site_configs = load_site_configs()
    if not site_configs:
        print("\n❌ Could not load site_configs.json!")
        return 1
    
    if args.all:
        results = {}
        for site_domain, theme_config in THEME_CONFIGS.items():
            upload_success, activation_success = deploy_theme(
                site_domain, theme_config, site_configs,
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
        
        upload_success, activation_success = deploy_theme(
            args.site, THEME_CONFIGS[args.site], site_configs,
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

