#!/usr/bin/env python3
"""
Enable WP_DEBUG for freerideinvestor.com
========================================

Enables WP_DEBUG to see actual PHP errors.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
import paramiko


def enable_debug():
    """Enable WP_DEBUG."""
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
        
        # Read current wp-config.php via SFTP
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        try:
            with deployer.sftp.open(wp_config, 'r') as f:
                config_content = f.read().decode('utf-8')
        except Exception as e:
            print(f"❌ Failed to read wp-config.php: {e}")
            return False
        
        # Check if WP_DEBUG is already enabled
        if "define('WP_DEBUG', true)" in config_content or 'define("WP_DEBUG", true)' in config_content:
            print("✅ WP_DEBUG already enabled")
            return True
        
        # Find insertion point (before "That's all")
        debug_lines = "\ndefine('WP_DEBUG', true);\ndefine('WP_DEBUG_LOG', true);\ndefine('WP_DEBUG_DISPLAY', false);\ndefine('SCRIPT_DEBUG', true);\n"
        
        if "That's all" in config_content:
            config_content = config_content.replace(
                "/* That's all",
                f"{debug_lines}/* That's all"
            )
        else:
            # Add before the closing PHP tag or at the end
            if "?>" in config_content:
                config_content = config_content.replace("?>", f"{debug_lines}?>")
            else:
                config_content = config_content + debug_lines
        
        # Write back
        try:
            with deployer.sftp.open(wp_config, 'w') as f:
                f.write(config_content.encode('utf-8'))
            print("✅ WP_DEBUG enabled successfully")
            print()
            print("💡 Error logs will be written to:")
            print(f"   {remote_path}/wp-content/debug.log")
            return True
        except Exception as e:
            print(f"❌ Failed to write wp-config.php: {e}")
            return False
        
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

