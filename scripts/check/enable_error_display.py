#!/usr/bin/env python3
"""
Temporarily enable error display to debug HTTP 500
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def enable_error_display(site_domain):
    """Temporarily enable error display."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔧 Enabling error display for {site_domain}...")

        # Read current wp-config.php
        result = deployer.execute_command('cat domains/freerideinvestor.com/public_html/wp-config.php')
        wp_config_content = result

        # Modify the debug settings to display errors
        modified_content = wp_config_content.replace(
            "define( 'WP_DEBUG_DISPLAY', false );",
            "define( 'WP_DEBUG_DISPLAY', true );"
        )

        modified_content = modified_content.replace(
            "@ini_set( 'display_errors', 0 );",
            "@ini_set( 'display_errors', 1 );"
        )

        # Write back the modified content
        # Create a temporary file and then move it
        temp_file = "/tmp/wp-config-debug.php"
        deployer.execute_command(f"echo '{modified_content}' > {temp_file}")
        deployer.execute_command(f"mv {temp_file} domains/freerideinvestor.com/public_html/wp-config.php")

        print("✅ Error display enabled")
        print("🔍 Test the website now to see actual error messages")
        print("⚠️  Remember to disable error display after debugging!")

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    enable_error_display('freerideinvestor.com')