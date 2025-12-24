#!/usr/bin/env python3
"""
Direct Theme Deployment (SFTP/SSH)
===================================

Deploys themes directly using paramiko/pysftp when WordPressManager is unavailable.
Falls back to creating deployment packages if SFTP libraries aren't available.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import os
import json
from pathlib import Path
from typing import Dict, Optional

# Theme deployment configuration
THEME_CONFIGS = {
    "houstonsipqueen.com": {
        "site_key": "houstonsipqueen",
        "theme_name": "houstonsipqueen",
        "theme_path": "websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen",
        "remote_path": "/wp-content/themes/houstonsipqueen"
    },
    "digitaldreamscape.site": {
        "site_key": "digitaldreamscape",
        "theme_name": "digitaldreamscape",
        "theme_path": "websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape",
        "remote_path": "/wp-content/themes/digitaldreamscape"
    }
}


def load_site_credentials():
    """Load site credentials from .deploy_credentials/sites.json"""
    creds_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if not creds_path.exists():
        # Try alternative locations
        alt_paths = [
            Path(".deploy_credentials/sites.json"),
            Path("../.deploy_credentials/sites.json"),
            Path("D:/websites/.deploy_credentials/sites.json")
        ]
        for alt_path in alt_paths:
            if alt_path.exists():
                creds_path = alt_path
                break
    
    if creds_path.exists():
        try:
            with open(creds_path, 'r') as f:
                return json.load(f)
        except Exception as e:
            print(f"⚠️  Could not load credentials: {e}")
            return {}
    return {}


def deploy_via_sftp(host, username, password, port, theme_path, remote_path):
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
        
        # Upload each file
        for file_path in theme_path.rglob('*'):
            if file_path.is_file():
                relative_path = file_path.relative_to(theme_path)
                remote_file = f"{remote_path}/{relative_path.as_posix()}"
                
                # Ensure remote directory exists
                remote_dir = str(Path(remote_file).parent)
                try:
                    sftp.stat(remote_dir)
                except FileNotFoundError:
                    # Create directory recursively
                    parts = remote_dir.strip('/').split('/')
                    current_path = ''
                    for part in parts:
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


def activate_via_wp_cli_ssh(host, username, password, port, theme_name, wp_path="/home/*/public_html"):
    """Activate theme via WP-CLI over SSH."""
    try:
        import paramiko
        
        print(f"🎨 Activating theme '{theme_name}' via SSH...")
        
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        # Execute WP-CLI command
        command = f"wp theme activate {theme_name} --path={wp_path}"
        stdin, stdout, stderr = ssh.exec_command(command)
        
        output = stdout.read().decode()
        error = stderr.read().decode()
        
        ssh.close()
        
        if "Success" in output or "Activated" in output:
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


def create_deployment_package(theme_path: Path, output_path: Path) -> bool:
    """Create a ZIP package for manual deployment."""
    try:
        import zipfile
        
        print(f"📦 Creating deployment package...")
        
        with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for file_path in theme_path.rglob('*'):
                if file_path.is_file():
                    arcname = file_path.relative_to(theme_path)
                    zipf.write(file_path, arcname)
                    print(f"   ✅ Added: {arcname}")
        
        print(f"✅ Package created: {output_path}")
        return True
    except Exception as e:
        print(f"❌ Error creating package: {e}")
        return False


def deploy_theme_direct(site_domain: str, config: dict, credentials: dict) -> tuple:
    """Deploy theme using direct SFTP/SSH."""
    base_path = Path("D:/websites")
    theme_path = base_path / config["theme_path"]
    
    if not theme_path.exists():
        print(f"❌ Theme path not found: {theme_path}")
        return False, False
    
    # Get credentials for this site
    site_key = config["site_key"]
    site_creds = credentials.get(site_key, {})
    
    if not site_creds:
        print(f"❌ No credentials found for {site_key}")
        print("   Creating deployment package instead...")
        
        # Create ZIP package as fallback
        output_path = base_path / f"{site_key}-theme.zip"
        if create_deployment_package(theme_path, output_path):
            print(f"\n📦 Deployment package ready: {output_path}")
            print("   Upload manually via WordPress admin:")
            print(f"   1. Go to: https://{site_domain}/wp-admin/theme-install.php?browse=upload")
            print(f"   2. Upload: {output_path}")
            print(f"   3. Activate theme")
        return False, False
    
    host = site_creds.get('host')
    username = site_creds.get('username')
    password = site_creds.get('password')
    port = site_creds.get('port', 22)
    
    if not all([host, username, password]):
        print(f"❌ Incomplete credentials for {site_key}")
        return False, False
    
    # Upload files
    upload_success = deploy_via_sftp(
        host, username, password, port,
        theme_path, config["remote_path"]
    )
    
    if not upload_success:
        return False, False
    
    # Activate theme
    activation_success = activate_via_wp_cli_ssh(
        host, username, password, port,
        config["theme_name"]
    )
    
    return upload_success, activation_success


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Deploy WordPress themes directly via SFTP/SSH'
    )
    parser.add_argument('--site', type=str, help='Site domain')
    parser.add_argument('--all', action='store_true', help='Deploy all themes')
    parser.add_argument('--upload-only', action='store_true', help='Upload only, don\'t activate')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🎨 DIRECT THEME DEPLOYMENT (SFTP/SSH)")
    print("="*60)
    
    # Load credentials
    credentials = load_site_credentials()
    if not credentials:
        print("\n⚠️  No credentials found!")
        print("   Expected: .deploy_credentials/sites.json")
        print("   Creating deployment packages instead...\n")
    
    if args.all:
        results = {}
        for site_domain, config in THEME_CONFIGS.items():
            print(f"\n{'='*60}")
            print(f"Processing: {site_domain}")
            print(f"{'='*60}")
            
            if credentials:
                upload_success, activation_success = deploy_theme_direct(
                    site_domain, config, credentials
                )
                results[site_domain] = {
                    'upload': upload_success,
                    'activation': activation_success if not args.upload_only else None
                }
            else:
                # Create deployment packages
                base_path = Path("D:/websites")
                theme_path = base_path / config["theme_path"]
                output_path = base_path / f"{config['site_key']}-theme.zip"
                
                if create_deployment_package(theme_path, output_path):
                    results[site_domain] = {
                        'upload': True,
                        'activation': None,
                        'package': str(output_path)
                    }
                else:
                    results[site_domain] = {'upload': False, 'activation': None}
        
        # Summary
        print("\n" + "="*60)
        print("📊 DEPLOYMENT SUMMARY")
        print("="*60)
        
        for site_domain, result in results.items():
            if 'package' in result:
                print(f"   {site_domain}: Package created at {result['package']}")
            else:
                upload_status = "✅" if result['upload'] else "❌"
                if result['activation'] is not None:
                    activation_status = "✅" if result['activation'] else "⚠️"
                    print(f"   {site_domain}:")
                    print(f"      Upload: {upload_status}")
                    print(f"      Activation: {activation_status}")
                else:
                    print(f"   {site_domain}: Upload {upload_status}")
        
        return 0
        
    elif args.site:
        if args.site not in THEME_CONFIGS:
            print(f"❌ Unknown site: {args.site}")
            return 1
        
        upload_success, activation_success = deploy_theme_direct(
            args.site, THEME_CONFIGS[args.site], credentials
        )
        
        return 0 if upload_success else 1
    else:
        parser.print_help()
        return 1


if __name__ == '__main__':
    exit(main())

