#!/usr/bin/env python3
"""
Check freerideinvestor.com Debug Log
=====================================

Reads and displays the WordPress debug.log file to identify runtime errors.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_debug_log():
    """Check WordPress debug.log for errors."""
    print("=" * 70)
    print("🔍 CHECKING FREERIDEINVESTOR.COM DEBUG LOG")
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
        
        # Check multiple possible locations for debug.log
        debug_log_paths = [
            f"{remote_path}/wp-content/debug.log",
            f"{remote_path}/debug.log",
            f"{remote_path}/error_log",
            f"{remote_path}/.error_log",
        ]
        
        print("📋 Checking for debug logs in common locations...")
        print()
        
        found_log = False
        for log_path in debug_log_paths:
            print(f"🔍 Checking: {log_path}")
            try:
                # Try to read the file using cat command
                log_content = deployer.execute_command(f"cat {log_path} 2>/dev/null")
                if log_content and log_content.strip():
                    print(f"   ✅ Found log file ({len(log_content)} bytes)")
                    print()
                    print("=" * 70)
                    print(f"📄 DEBUG LOG CONTENT: {log_path}")
                    print("=" * 70)
                    print()
                    # Show last 50 lines (most recent errors)
                    lines = log_content.split('\n')
                    recent_lines = lines[-50:] if len(lines) > 50 else lines
                    print('\n'.join(recent_lines))
                    print()
                    found_log = True
                    break
                else:
                    print("   ⚠️  File not found or empty")
            except Exception as e:
                print(f"   ⚠️  Error reading file: {e}")
            print()
        
        if not found_log:
            print("⚠️  No debug log found in common locations")
            print()
            print("💡 Debug log locations to check manually:")
            print("   - wp-content/debug.log (if WP_DEBUG_LOG is enabled)")
            print("   - error_log (server-level error log)")
            print("   - .error_log (alternative server log)")
            print("   - Check hosting panel error logs")
            print()
            print("🔧 To enable debug logging, ensure wp-config.php has:")
            print("   define('WP_DEBUG', true);")
            print("   define('WP_DEBUG_LOG', true);")
            print("   define('WP_DEBUG_DISPLAY', false);")
        
        # Also check server error logs via command
        print()
        print("=" * 70)
        print("🔍 CHECKING SERVER ERROR LOGS")
        print("=" * 70)
        print()
        
        error_log_commands = [
            f"tail -n 50 {remote_path}/error_log 2>/dev/null",
            f"tail -n 50 {remote_path}/.error_log 2>/dev/null",
            f"find {remote_path} -name 'error_log' -o -name 'debug.log' 2>/dev/null | head -5",
        ]
        
        for cmd in error_log_commands:
            print(f"📋 Running: {cmd}")
            result = deployer.execute_command(cmd)
            if result and result.strip():
                print(f"   ✅ Output:")
                print(result[:1000])  # Show first 1000 chars
                print()
            else:
                print("   ⚠️  No output")
            print()
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(check_debug_log())
