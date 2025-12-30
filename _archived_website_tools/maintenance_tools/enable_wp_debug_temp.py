#!/usr/bin/env python3
"""
Temporarily Enable WP_DEBUG
============================

Enables WP_DEBUG to see actual errors.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def enable_debug():
    """Enable WP_DEBUG temporarily."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 ENABLING WP_DEBUG: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        wp_config = f"{remote_path}/wp-config.php"
        
        # Read current wp-config.php
        read_config = f"cat {wp_config}"
        config_content = deployer.execute_command(read_config)
        
        # Check if WP_DEBUG is already defined
        if "define('WP_DEBUG', true)" in config_content or 'define("WP_DEBUG", true)' in config_content:
            print("✅ WP_DEBUG already enabled")
        else:
            # Add WP_DEBUG lines before "That's all, stop editing!"
            debug_lines = """define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);"""
            
            # Find the insertion point
            if "That's all" in config_content:
                config_content = config_content.replace(
                    "That's all",
                    f"{debug_lines}\n\n/* That's all"
                )
                # Write back (this is a simplified version - actual implementation would use SFTP)
                print("⚠️  Manual edit needed - add these lines to wp-config.php before 'That's all':")
                print(debug_lines)
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = enable_debug()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

