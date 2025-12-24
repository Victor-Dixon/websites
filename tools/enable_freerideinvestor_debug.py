#!/usr/bin/env python3
"""
Enable WordPress Debug Mode for freerideinvestor.com
=====================================================

Enables WP_DEBUG and WP_DEBUG_LOG to capture runtime errors.

Author: Agent-3
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def enable_debug():
    """Enable WordPress debug mode."""
    print("=" * 70)
    print("🔧 ENABLING WORDPRESS DEBUG MODE")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return 1
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return 1
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        wp_config = f"{remote_path}/wp-config.php"
        
        print("📖 Reading wp-config.php...")
        command = f"cat {wp_config}"
        config_content = deployer.execute_command(command)
        
        if not config_content:
            print("❌ Cannot read wp-config.php")
            return 1
        
        # Check if debug is already enabled
        if "define('WP_DEBUG', true);" in config_content:
            print("   ✅ WP_DEBUG is already enabled")
        else:
            # Enable debug
            print("   🔧 Enabling WP_DEBUG...")
            # Remove any existing WP_DEBUG lines
            lines = config_content.split('\n')
            new_lines = []
            skip_next = False
            for line in lines:
                if 'WP_DEBUG' in line and 'define' in line:
                    skip_next = True
                    continue
                if skip_next and line.strip() == '':
                    skip_next = False
                new_lines.append(line)
            
            # Add debug settings before "That's all, stop editing!"
            final_lines = []
            debug_added = False
            for line in new_lines:
                if "That's all, stop editing!" in line and not debug_added:
                    final_lines.append("")
                    final_lines.append("// Enable WordPress debug mode")
                    final_lines.append("define('WP_DEBUG', true);")
                    final_lines.append("define('WP_DEBUG_LOG', true);")
                    final_lines.append("define('WP_DEBUG_DISPLAY', false);")
                    final_lines.append("@ini_set('display_errors', 0);")
                    final_lines.append("")
                    debug_added = True
                final_lines.append(line)
            
            new_content = '\n'.join(final_lines)
            
            # Create backup
            print("   💾 Creating backup...")
            deployer.execute_command(f"cp {wp_config} {wp_config}.backup.$(date +%Y%m%d_%H%M%S)")
            
            # Save locally
            local_file = Path(__file__).parent.parent / "docs" / "wp-config-debug-enabled.php"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(new_content, encoding='utf-8')
            
            # Deploy
            print("   🚀 Deploying updated wp-config.php...")
            success = deployer.deploy_file(local_file, wp_config)
            
            if success:
                print("   ✅ Debug mode enabled!")
            else:
                print("   ❌ Failed to deploy")
                return 1
        
        # Verify debug.log exists and is writable
        debug_log = f"{remote_path}/wp-content/debug.log"
        print(f"\n📝 Checking debug.log: {debug_log}")
        command = f"touch {debug_log} && chmod 666 {debug_log} && ls -la {debug_log}"
        result = deployer.execute_command(command)
        print(f"   {result}")
        
        print("\n✅ Debug mode setup complete!")
        print("   📝 Errors will now be logged to wp-content/debug.log")
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(enable_debug())

