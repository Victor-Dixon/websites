#!/usr/bin/env python3
"""
Quick Fix Script for WordPress Configuration
===========================================

This script automatically configures wp-config.php files for sites
that are currently showing HTTP 500 errors due to missing database
credentials.

Author: The Swarm (Multi-Agent AI System)
Date: 2026-01-01
"""

import os
import secrets
import string
from pathlib import Path
import shutil
from datetime import datetime

class QuickFixConfigManager:
    """Quick fix for WordPress configuration issues."""

    def __init__(self):
        self.project_root = Path(__file__).parent
        self.websites_path = self.project_root / "websites"
        self.config_backup_path = self.project_root / "config" / "backups"

        # Create backup directory
        self.config_backup_path.mkdir(parents=True, exist_ok=True)

    def generate_wordpress_salts(self) -> dict:
        """Generate secure WordPress salts."""
        def generate_salt(length=64):
            chars = string.ascii_letters + string.digits + "!@#$%^&*()-_=+[]{}|;:,.<>?"
            return ''.join(secrets.choice(chars) for _ in range(length))

        salts = {
            'AUTH_KEY': generate_salt(),
            'SECURE_AUTH_KEY': generate_salt(),
            'LOGGED_IN_KEY': generate_salt(),
            'NONCE_KEY': generate_salt(),
            'AUTH_SALT': generate_salt(),
            'SECURE_AUTH_SALT': generate_salt(),
            'LOGGED_IN_SALT': generate_salt(),
            'NONCE_SALT': generate_salt(),
        }

        return salts

    def fix_wp_config(self, site_domain: str) -> bool:
        """Fix wp-config.php with proper database configuration."""
        site_path = self.websites_path / site_domain
        wp_config = site_path / "wp-config.php"

        if not wp_config.exists():
            print(f"❌ wp-config.php not found for {site_domain}")
            return False

        # Backup existing config
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        backup_name = f"{site_domain}_wp-config.php.{timestamp}"
        backup_path = self.config_backup_path / backup_name

        try:
            shutil.copy2(wp_config, backup_path)
            print(f"✅ Configuration backed up: {backup_name}")
        except Exception as e:
            print(f"⚠️  Backup failed: {e}")

        try:
            # Read existing config
            with open(wp_config, 'r') as f:
                content = f.read()

            # Generate database configuration
            db_name = f"{site_domain.replace('.', '_')}_db"
            db_user = f"{site_domain.replace('.', '_')}_user"
            db_password = self.generate_secure_password()
            db_host = 'localhost'

            # Generate salts
            salts = self.generate_wordpress_salts()

            # Update database settings
            replacements = {
                f"define( 'DB_NAME', '{site_domain.replace('.', '_')}_db' );": f"define( 'DB_NAME', '{db_name}' );",
                f"define( 'DB_USER', '{site_domain.replace('.', '_')}_user' );": f"define( 'DB_USER', '{db_user}' );",
                f"define( 'DB_PASSWORD', '{site_domain.replace('.', '_')}_password' );": f"define( 'DB_PASSWORD', '{db_password}' );",
                "define( 'DB_HOST', 'localhost' );": f"define( 'DB_HOST', '{db_host}' );",
            }

            for old, new in replacements.items():
                content = content.replace(old, new)

            # Update salts
            for salt_key, salt_value in salts.items():
                old_salt = f"define( '{salt_key}', 'put your unique phrase here' );"
                new_salt = f"define( '{salt_key}', '{salt_value}' );"
                content = content.replace(old_salt, new_salt)

            # Write updated config
            with open(wp_config, 'w') as f:
                f.write(content)

            print(f"✅ Fixed wp-config.php for {site_domain}")
            print(f"   Database: {db_name}")
            print(f"   User: {db_user}")
            print(f"   Password: {'*' * len(db_password)}")
            print(f"   Host: {db_host}")

            return True

        except Exception as e:
            print(f"❌ Failed to fix wp-config.php: {e}")
            return False

    def generate_secure_password(self, length=16) -> str:
        """Generate a secure password."""
        chars = string.ascii_letters + string.digits + "!@#$%^&*"
        return ''.join(secrets.choice(chars) for _ in range(length))

def main():
    """Main entry point."""
    print("🔧 QUICK WORDPRESS CONFIG FIX")
    print("=" * 50)

    fixer = QuickFixConfigManager()

    # Sites that need fixing
    broken_sites = ['freerideinvestor.com', 'prismblossom.online']

    success_count = 0
    for site in broken_sites:
        print(f"\n🔧 Fixing {site}...")
        if fixer.fix_wp_config(site):
            success_count += 1
        else:
            print(f"❌ Failed to fix {site}")

    print("\n📊 FIX SUMMARY")
    print("=" * 30)
    print(f"✅ Successfully fixed: {success_count}/{len(broken_sites)}")

    if success_count == len(broken_sites):
        print("\n🚀 NEXT STEPS:")
        print("1. Run the audit script to verify fixes: python audit_websites.py")
        print("2. Deploy configurations if needed: python main.py config deploy --site <domain>")
        print("3. Test websites in browser")
    else:
        print("\n❌ Some fixes failed. Check the error messages above.")

if __name__ == '__main__':
    main()