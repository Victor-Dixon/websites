#!/usr/bin/env python3
"""
Deploy Houston Sip Queen Theme Files
=====================================

Deploys all theme files for houstonsipqueen.com to the live server.

Usage:
    python deploy_houstonsipqueen.py
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_KEY = "houstonsipqueen.com"
THEME_PATH = Path(__file__).resolve().parents[2] / "websites" / "houstonsipqueen.com" / "wp" / "wp-content" / "themes" / "houstonsipqueen"
REMOTE_THEME_PATH = "wp-content/themes/houstonsipqueen"


def main():
    """Deploy Houston Sip Queen theme files."""
    print(f"\n{'='*70}")
    print(f"🚀 DEPLOYING: {SITE_KEY}")
    print(f"{'='*70}\n")
    
    # Load site configurations
    print("📋 Loading site configurations...")
    site_configs = load_site_configs()
    
    if SITE_KEY not in site_configs:
        # Try to find by domain match
        found = False
        for key in site_configs.keys():
            if SITE_KEY in key or key.endswith(SITE_KEY):
                print(f"✅ Found config: {key}")
                found = True
                break
        
        if not found:
            print(f"❌ Site '{SITE_KEY}' not found in configuration")
            print(f"   Available sites: {list(site_configs.keys())}")
            return False
    
    # Initialize deployer
    try:
        deployer = SimpleWordPressDeployer(SITE_KEY, site_configs)
    except ValueError as e:
        print(f"❌ {e}")
        return False
    
    # Connect to server
    print(f"\n🔌 Connecting to server...")
    if not deployer.connect():
        print(f"❌ Failed to connect to {SITE_KEY}")
        return False
    
    # Check theme directory exists
    if not THEME_PATH.exists():
        print(f"❌ Theme directory not found: {THEME_PATH}")
        deployer.disconnect()
        return False
    
    print(f"📂 Theme directory: {THEME_PATH}")
    print(f"📤 Remote path: {REMOTE_THEME_PATH}\n")
    
    # Get remote base path from config
    remote_base = REMOTE_THEME_PATH
    site_config = site_configs.get(SITE_KEY, {})
    if 'sftp' in site_config:
        remote_path = site_config['sftp'].get('remote_path', '')
        if remote_path:
            remote_base = f"{remote_path}/{REMOTE_THEME_PATH}"
    elif 'remote_path' in site_config:
        remote_path = site_config.get('remote_path', '')
        if remote_path:
            remote_base = f"{remote_path}/{REMOTE_THEME_PATH}"
    
    # Deploy all files
    uploaded_count = 0
    failed_count = 0
    skipped_count = 0
    
    # Get all files to deploy
    files_to_deploy = []
    for file_path in THEME_PATH.rglob('*'):
        if file_path.is_file():
            # Skip if file doesn't exist (broken symlinks, etc.)
            if not file_path.exists():
                skipped_count += 1
                print(f"⚠️  Skipping non-existent file: {file_path.name}")
                continue
            
            files_to_deploy.append(file_path)
    
    print(f"📦 Found {len(files_to_deploy)} files to deploy\n")
    
    for file_path in files_to_deploy:
        # Get relative path from theme directory
        relative_path = file_path.relative_to(THEME_PATH)
        
        # Construct remote path
        remote_file_path = f"{remote_base}/{relative_path.as_posix()}"
        
        print(f"📤 Uploading: {relative_path}...", end=" ")
        
        try:
            if deployer.deploy_file(file_path, remote_file_path):
                print("✅")
                uploaded_count += 1
                
                # Check PHP syntax for PHP files
                if file_path.suffix == '.php':
                    syntax_check = deployer.check_php_syntax(remote_file_path)
                    if not syntax_check.get('valid', True):
                        print(f"   ⚠️  PHP Syntax Error on line {syntax_check.get('line_number', '?')}: {syntax_check.get('error_message', 'Unknown error')}")
            else:
                print("❌")
                failed_count += 1
        except Exception as e:
            print(f"❌ Error: {e}")
            failed_count += 1
    
    # Disconnect
    deployer.disconnect()
    
    # Summary
    print(f"\n{'='*70}")
    print(f"📊 DEPLOYMENT SUMMARY")
    print(f"{'='*70}")
    print(f"✅ Uploaded: {uploaded_count}")
    print(f"❌ Failed: {failed_count}")
    print(f"⚠️  Skipped: {skipped_count}")
    print(f"📦 Total: {len(files_to_deploy)}")
    
    if failed_count == 0:
        print(f"\n✅ Deployment successful!")
        return True
    else:
        print(f"\n⚠️  Deployment completed with {failed_count} errors")
        return False


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)


