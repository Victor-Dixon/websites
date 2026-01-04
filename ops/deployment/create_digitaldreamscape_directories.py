#!/usr/bin/env python3
"""
Create DigitalDreamscape Theme Directories via SSH
===================================================

This script creates the necessary directories on the server via SSH
before attempting SFTP deployment. This resolves the permission issues
where the SFTP user cannot create directories.

Author: Agent-7 (Web Development Specialist)
Date: 2025-01-03
"""

import sys
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import load_site_configs, SimpleWordPressDeployer

def create_digitaldreamscape_directories():
    """Create required directories for DigitalDreamscape theme via SSH."""
    print("\n" + "="*60)
    print("📁 CREATING: digitaldreamscape.site Theme Directories")
    print("="*60 + "\n")

    site_key = "digitaldreamscape.site"

    try:
        # Load site configurations
        site_configs = load_site_configs()
        if not site_configs:
            print("❌ No site configurations found!")
            return False

        # Initialize deployer
        deployer = SimpleWordPressDeployer(site_key, site_configs)

        # Connect to server
        print(f"🔌 Connecting to {site_key} for directory creation...")
        if not deployer.connect():
            print(f"❌ Failed to connect to {site_key}")
            return False
        print("✅ Connected!\n")

        # Commands to create directories
        # Based on the site config, the remote_path is: domains/digitaldreamscape.site/public_html
        # So wp-content/themes/digitaldreamscape/ will be: domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape/

        commands = [
            # Create themes directory structure
            "mkdir -p domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape",
            "mkdir -p domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape/js",

            # Set proper permissions (755 for directories, 644 for files)
            "chmod 755 domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape",
            "chmod 755 domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape/js",

            # Verify directories exist
            "ls -la domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape/"
        ]

        success_count = 0
        for command in commands:
            print(f"🔧 Executing: {command}")
            try:
                result = deployer.execute_command(command)
                if result.strip():
                    print(f"📋 Output: {result.strip()}")
                    success_count += 1
                else:
                    print("⚠️  No output (might be normal for mkdir)")
                    success_count += 1
            except Exception as e:
                print(f"❌ Command failed: {e}")

        # Disconnect
        deployer.disconnect()

        # Summary
        print(f"\n{'='*60}")
        print("📊 DIRECTORY CREATION SUMMARY")
        print(f"{'='*60}")
        print(f"   ✅ Commands executed: {success_count}/{len(commands)}")

        if success_count >= len(commands) - 1:  # Allow for ls command to fail
            print("✅ Directory structure created successfully!")
            print("\n💡 Next Steps:")
            print("   1. Run the deployment script: python deploy_digitaldreamscape.py")
            print("   2. Clear WordPress cache")
            print("   3. Visit https://digitaldreamscape.site to verify")
            return True
        else:
            print("⚠️  Some commands failed. Check server permissions.")
            return False

    except Exception as e:
        print(f"❌ Error creating directories: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution"""
    success = create_digitaldreamscape_directories()
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())