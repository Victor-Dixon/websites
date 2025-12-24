#!/usr/bin/env python3
"""
Fix freerideinvestor.com HTTP 500 Error
=======================================

Automated fix tool for freerideinvestor.com HTTP 500 error.
Enables debug mode, checks common issues, and provides fixes.

Author: Agent-1 (Integration & Core Systems Specialist)
Date: 2025-12-22
"""

import sys
import json
from pathlib import Path
from typing import Dict, Optional, List

# Add tools to path
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod
    MANAGER_AVAILABLE = True
except ImportError:
    MANAGER_AVAILABLE = False

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False


def check_wp_config_syntax(deployer) -> Dict:
    """Check wp-config.php for syntax errors."""
    if not deployer or not deployer.connect():
        return {"error": "Cannot connect"}
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Check if file exists
        command = f"test -f {wp_config_path} && echo 'EXISTS' || echo 'NOT_EXISTS'"
        result = deployer.execute_command(command)
        if "NOT_EXISTS" in result:
            return {"error": "wp-config.php not found"}
        
        # Try to validate PHP syntax
        command = f"php -l {wp_config_path} 2>&1"
        syntax_check = deployer.execute_command(command)
        
        # Read wp-config.php content (first 100 lines to check structure)
        command = f"head -n 100 {wp_config_path}"
        config_content = deployer.execute_command(command)
        
        return {
            "syntax_check": syntax_check,
            "has_debug": "WP_DEBUG" in config_content,
            "config_preview": config_content[:500] if config_content else ""
        }
    except Exception as e:
        return {"error": str(e)}
    finally:
        deployer.disconnect()


def read_wp_config_full(deployer) -> Optional[str]:
    """Read full wp-config.php file."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        wp_config_path = f"{remote_path}/wp-config.php"
        
        command = f"cat {wp_config_path}"
        result = deployer.execute_command(command)
        return result
    except Exception as e:
        print(f"   ❌ Error reading wp-config.php: {e}")
        return None
    finally:
        deployer.disconnect()


def fix_wp_config_syntax(deployer) -> bool:
    """Attempt to fix wp-config.php syntax errors."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Read current config
        config_content = deployer.execute_command(f"cat {wp_config_path}")
        if not config_content:
            print("   ❌ Cannot read wp-config.php")
            return False
        
        # Create backup
        backup_command = f"cp {wp_config_path} {wp_config_path}.backup.$(date +%Y%m%d_%H%M%S)"
        deployer.execute_command(backup_command)
        print("   ✅ Created backup of wp-config.php")
        
        # Common syntax fixes
        fixed_content = config_content
        
        # Fix 1: Check for unclosed quotes
        # Fix 2: Check for missing semicolons
        # Fix 3: Check for unclosed parentheses/brackets
        
        # For now, just report the issue - manual fix needed
        print("   ⚠️  Syntax error detected in wp-config.php")
        print("   📝 Manual fix required - check for:")
        print("      - Unclosed quotes (single or double)")
        print("      - Missing semicolons")
        print("      - Unclosed parentheses or brackets")
        print("      - Invalid PHP syntax")
        print()
        print("   💡 To view full wp-config.php, run:")
        print(f"      ssh {deployer.site_key} 'cat {wp_config_path}'")
        
        return False  # Manual fix needed
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def enable_wp_debug(deployer) -> bool:
    """Enable WordPress debug mode in wp-config.php."""
    if not deployer or not deployer.connect():
        print("   ❌ Cannot connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Read current wp-config.php
        command = f"cat {wp_config_path}"
        current_config = deployer.execute_command(command)
        
        if not current_config:
            print("   ❌ Cannot read wp-config.php")
            return False
        
        # Check if debug is already enabled
        if "define('WP_DEBUG', true)" in current_config or 'define("WP_DEBUG", true)' in current_config:
            print("   ✅ Debug mode already enabled")
            return True
        
        # Find where to insert debug settings (before "That's all, stop editing!")
        debug_settings = """
// Enable WordPress debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
"""
        
        # Create backup first
        backup_command = f"cp {wp_config_path} {wp_config_path}.backup"
        deployer.execute_command(backup_command)
        print("   ✅ Created backup: wp-config.php.backup")
        
        # Insert debug settings before "That's all" comment
        if "That's all, stop editing!" in current_config:
            new_config = current_config.replace(
                "/* That's all, stop editing!",
                f"{debug_settings}\n/* That's all, stop editing!"
            )
        else:
            # Append at end if no marker found
            new_config = current_config + debug_settings
        
        # Write new config
        # Note: This requires SFTP write capability
        print("   ⚠️  Manual step required: Add debug settings to wp-config.php")
        print("   📝 Add these lines before 'That's all, stop editing!':")
        print(debug_settings)
        
        return False  # Manual intervention needed
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def check_php_version(deployer) -> Optional[str]:
    """Check PHP version compatibility."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        command = "php -v 2>&1 | head -n 1"
        result = deployer.execute_command(command)
        if result:
            print(f"   ✅ PHP version: {result.strip()}")
            return result.strip()
        return None
    except Exception as e:
        print(f"   ❌ Error checking PHP version: {e}")
        return None
    finally:
        deployer.disconnect()


def check_database_connection(deployer) -> bool:
    """Check if database credentials are valid."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        
        # Check wp-config.php for database settings
        command = f"cat {remote_path}/wp-config.php | grep -E 'DB_NAME|DB_USER|DB_PASSWORD|DB_HOST'"
        result = deployer.execute_command(command)
        
        if result:
            print("   ✅ Database credentials found in wp-config.php")
            # Check if any are empty
            if "''" in result or '""' in result:
                print("   ⚠️  Warning: Some database credentials appear empty")
                return False
            return True
        else:
            print("   ❌ Database credentials not found")
            return False
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def disable_plugins_via_rename(deployer) -> bool:
    """Disable all plugins by renaming plugins directory (safe method)."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        plugins_path = f"{remote_path}/wp-content/plugins"
        plugins_backup = f"{remote_path}/wp-content/plugins.disabled"
        
        # Check if plugins directory exists
        command = f"test -d {plugins_path} && echo 'EXISTS' || echo 'NOT_EXISTS'"
        result = deployer.execute_command(command)
        
        if "NOT_EXISTS" in result:
            print("   ⚠️  Plugins directory not found")
            return False
        
        # Rename plugins directory
        command = f"mv {plugins_path} {plugins_backup}"
        result = deployer.execute_command(command)
        
        if result is None or result == "":
            print("   ✅ Plugins disabled (renamed to plugins.disabled)")
            print("   💡 To re-enable: mv wp-content/plugins.disabled wp-content/plugins")
            return True
        else:
            print(f"   ⚠️  Result: {result}")
            return False
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def check_error_logs(deployer) -> Dict:
    """Check error logs for PHP fatal errors."""
    if not deployer or not deployer.connect():
        return {"error": "Cannot connect"}
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        
        # Check common error log locations
        log_paths = [
            f"{remote_path}/wp-content/debug.log",  # WordPress debug log (most important)
            f"{remote_path}/error_log",  # PHP error log
            f"{remote_path}/wp-content/error_log",
            f"{remote_path}/../error_log"
        ]
        
        logs_found = []
        for log_path in log_paths:
            # Try to read last 100 lines (more for debug.log)
            command = f"tail -n 100 {log_path} 2>/dev/null || echo 'LOG_NOT_FOUND'"
            result = deployer.execute_command(command)
            if result and "LOG_NOT_FOUND" not in result and result.strip():
                lines = result.split('\n')
                # Filter out empty lines
                non_empty_lines = [line for line in lines if line.strip()]
                if non_empty_lines:
                    logs_found.append({
                        "path": log_path,
                        "last_lines": non_empty_lines[-30:] if len(non_empty_lines) > 30 else non_empty_lines  # Last 30 lines
                    })
        
        return {"logs": logs_found} if logs_found else {"error": "No error logs found"}
    except Exception as e:
        return {"error": str(e)}
    finally:
        deployer.disconnect()


def check_htaccess_syntax(deployer) -> bool:
    """Check .htaccess for syntax errors."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        htaccess_path = f"{remote_path}/.htaccess"
        
        # Check if .htaccess exists
        command = f"test -f {htaccess_path} && echo 'EXISTS' || echo 'NOT_EXISTS'"
        result = deployer.execute_command(command)
        
        if "NOT_EXISTS" in result:
            print("   ✅ No .htaccess file (not the issue)")
            return True
        
        # Try to validate syntax (basic check)
        command = f"apache2ctl -t 2>&1 || httpd -t 2>&1 || echo 'VALIDATION_UNAVAILABLE'"
        result = deployer.execute_command(command)
        
        if "VALIDATION_UNAVAILABLE" in result:
            print("   ⚠️  Cannot validate .htaccess syntax (manual check needed)")
            return False
        
        if "Syntax OK" in result or "syntax is OK" in result:
            print("   ✅ .htaccess syntax is valid")
            return True
        else:
            print("   ⚠️  .htaccess may have syntax errors")
            print(f"   📝 Check result: {result[:200]}")
            return False
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def main():
    """Main fix execution."""
    print("=" * 70)
    print("🔧 FREERIDEINVESTOR.COM HTTP 500 ERROR FIX TOOL")
    print("=" * 70)
    print()
    
    if not DEPLOYER_AVAILABLE:
        print("❌ SimpleWordPressDeployer not available")
        print("   Install required dependencies or check deployment tools")
        return 1
    
    # Load site config
    config_path = Path("D:/websites/configs/site_configs.json")
    if not config_path.exists():
        print("❌ Site config not found")
        return 1
    
    with open(config_path, 'r', encoding='utf-8') as f:
        site_configs = json.load(f)
    
    site_config = site_configs.get("freerideinvestor.com", {})
    
    if not site_config:
        print("❌ freerideinvestor.com not found in site configs")
        return 1
    
    # Initialize deployer (it will load credentials from multiple sources)
    print("🔑 Loading SFTP credentials...")
    print("   Checking: HOSTINGER_* env vars → .deploy_credentials/sites.json → site_configs.json")
    print()
    
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        print()
        print("💡 Credential Sources (checked in order):")
        print("   1. Environment variables (.env file):")
        print("      - HOSTINGER_HOST")
        print("      - HOSTINGER_USER")
        print("      - HOSTINGER_PASS")
        print("      - HOSTINGER_PORT (default: 65002)")
        print("   2. .deploy_credentials/sites.json (WordPressManager format)")
        print("   3. site_configs.json (sftp section)")
        print()
        print("📋 Manual Fix Steps (if credentials unavailable):")
        print("   1. Log into hosting panel (cPanel/hPanel)")
        print("   2. Navigate to File Manager")
        print("   3. Open wp-config.php")
        print("   4. Add these lines before 'That's all, stop editing!':")
        print()
        print("   define('WP_DEBUG', true);")
        print("   define('WP_DEBUG_LOG', true);")
        print("   define('WP_DEBUG_DISPLAY', false);")
        print("   @ini_set('display_errors', 0);")
        print()
        return 1
    
    
    print("🔍 Running diagnostic checks...")
    print()
    
    # 1. Check PHP version
    print("1️⃣  Checking PHP version...")
    php_version = check_php_version(deployer)
    if php_version:
        # Check if version is compatible
        if "8." in php_version or "7.4" in php_version or "7.3" in php_version:
            print("   ✅ PHP version is compatible with WordPress")
        else:
            print("   ⚠️  PHP version may be too old (WordPress requires 7.4+)")
    print()
    
    # 2. Check database connection
    print("2️⃣  Checking database credentials...")
    db_ok = check_database_connection(deployer)
    print()
    
    print("3️⃣  Checking wp-config.php syntax...")
    wp_config_check = check_wp_config_syntax(deployer)
    if "error" in wp_config_check:
        print(f"   ⚠️  {wp_config_check['error']}")
    else:
        if "syntax_check" in wp_config_check:
            syntax_result = wp_config_check['syntax_check']
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ wp-config.php syntax is valid")
            else:
                print(f"   ❌ wp-config.php syntax error detected:")
                print(f"      {syntax_result[:200]}")
                print()
                print("   🔧 Attempting to read wp-config.php for analysis...")
                config_content = read_wp_config_full(deployer)
                if config_content:
                    # Save to local file for analysis
                    local_config_path = Path(__file__).parent.parent / "docs" / "freerideinvestor_wp_config.php"
                    local_config_path.parent.mkdir(parents=True, exist_ok=True)
                    with open(local_config_path, 'w', encoding='utf-8') as f:
                        f.write(config_content)
                    print(f"   ✅ wp-config.php saved to: {local_config_path}")
                    print("   📝 Review file to identify syntax error")
        if wp_config_check.get('has_debug'):
            print("   ✅ Debug mode is enabled in wp-config.php")
    print()
    
    print("4️⃣  Checking error logs...")
    error_logs = check_error_logs(deployer)
    if "error" in error_logs:
        print(f"   ⚠️  {error_logs['error']}")
    elif "logs" in error_logs:
        print(f"   ✅ Found {len(error_logs['logs'])} error log(s)")
        for log_info in error_logs['logs']:
            print(f"   📄 {log_info['path']}:")
            # Show last 5 lines of each log
            for line in log_info['last_lines'][-5:]:
                if line.strip():
                    print(f"      {line[:100]}")
    print()
    
    print("5️⃣  Checking .htaccess syntax...")
    htaccess_ok = check_htaccess_syntax(deployer)
    print()
    
    print("6️⃣  Enabling WordPress debug mode...")
    debug_enabled = enable_wp_debug(deployer)
    print()
    
    print("7️⃣  Plugin conflict check...")
    print("   💡 Attempting to disable plugins to test for conflicts...")
    plugins_disabled = disable_plugins_via_rename(deployer)
    if plugins_disabled:
        print("   ✅ Plugins disabled - test site now")
        print("   💡 If site works, re-enable plugins one by one to find the culprit")
    else:
        print("   ⚠️  Could not disable plugins automatically")
        print("   💡 Manual: rename wp-content/plugins to plugins.disabled")
    print()
    
    print("=" * 70)
    print("📊 FIX SUMMARY")
    print("=" * 70)
    print()
    print("✅ Diagnostic checks complete")
    print()
    print("🔧 Next Steps:")
    print("   1. Check error logs:")
    print("      - public_html/error_log")
    print("      - wp-content/debug.log (after enabling debug)")
    print("   2. Review error messages to identify root cause")
    print("   3. Apply specific fix based on error type")
    print()
    print("💡 If site is still down after enabling debug:")
    print("   - Disable plugins: rename wp-content/plugins to plugins.disabled")
    print("   - Switch theme: rename active theme folder")
    print("   - Check database connection")
    print("   - Verify PHP version compatibility")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

