#!/usr/bin/env python3
"""
Diagnose freerideinvestor.com HTTP 500 Error
============================================

Comprehensive diagnostic tool for freerideinvestor.com HTTP 500 error.
Uses unified WordPress manager to check site health and diagnose issues.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

# Add tools to path
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod
    MANAGER_AVAILABLE = True
except ImportError:
    MANAGER_AVAILABLE = False
    print("❌ unified_wordpress_manager not available")

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False


def check_error_logs(deployer):
    """Check WordPress error logs via SFTP."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/freerideinvestor.com/public_html"
        
        # Check common error log locations
        log_paths = [
            f"{remote_path}/wp-content/debug.log",
            f"{remote_path}/error_log",
            f"{remote_path}/wp-content/error_log",
            f"{remote_path}/../error_log"
        ]
        
        logs_found = []
        for log_path in log_paths:
            try:
                # Try to read last 50 lines
                command = f"tail -n 50 {log_path} 2>/dev/null || echo 'LOG_NOT_FOUND'"
                result = deployer.execute_command(command)
                if result and "LOG_NOT_FOUND" not in result:
                    logs_found.append({
                        "path": log_path,
                        "last_lines": result.split('\n')[-20:]  # Last 20 lines
                    })
            except Exception:
                pass
        
        return logs_found if logs_found else None
    finally:
        deployer.disconnect()


def check_wp_config(deployer):
    """Check wp-config.php for debug settings."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/freerideinvestor.com/public_html"
        
        command = f"cd {remote_path} && cat wp-config.php | grep -E 'WP_DEBUG|WP_DEBUG_LOG|WP_DEBUG_DISPLAY|error_reporting' || echo 'NO_DEBUG_SETTINGS'"
        result = deployer.execute_command(command)
        
        return result if result and "NO_DEBUG_SETTINGS" not in result else None
    finally:
        deployer.disconnect()


def check_php_version(deployer):
    """Check PHP version."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        command = "php -v 2>&1 | head -n 1"
        result = deployer.execute_command(command)
        return result.strip() if result else None
    finally:
        deployer.disconnect()


def check_database_connection(deployer):
    """Check WordPress database connectivity via WP-CLI."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/freerideinvestor.com/public_html"
        
        command = f"cd {remote_path} && wp db check --allow-root 2>&1"
        result = deployer.execute_command(command)
        return result.strip() if result else None
    finally:
        deployer.disconnect()


def check_plugin_conflicts(deployer):
    """Check for plugin conflicts."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/freerideinvestor.com/public_html"
        
        # List plugins
        command = f"cd {remote_path} && wp plugin list --status=active --format=json --allow-root 2>&1"
        result = deployer.execute_command(command)
        return result.strip() if result else None
    finally:
        deployer.disconnect()


def check_theme_status(deployer):
    """Check active theme status."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/freerideinvestor.com/public_html"
        
        command = f"cd {remote_path} && wp theme list --status=active --format=json --allow-root 2>&1"
        result = deployer.execute_command(command)
        return result.strip() if result else None
    finally:
        deployer.disconnect()


def check_file_permissions(deployer):
    """Check critical file permissions."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/freerideinvestor.com/public_html"
        
        files_to_check = [
            "wp-config.php",
            "index.php",
            ".htaccess"
        ]
        
        permissions = {}
        for file in files_to_check:
            command = f"cd {remote_path} && ls -la {file} 2>&1"
            result = deployer.execute_command(command)
            if result:
                permissions[file] = result.strip()
        
        return permissions if permissions else None
    finally:
        deployer.disconnect()


def main():
    """Main diagnostic execution."""
    print("=" * 70)
    print("🔍 FREERIDEINVESTOR.COM HTTP 500 ERROR DIAGNOSTIC")
    print("=" * 70)
    print()
    
    # Load site configs
    import json
    config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
    if not config_path.exists():
        print("❌ site_configs.json not found")
        return 1
    
    with open(config_path, 'r', encoding='utf-8') as f:
        site_configs = json.load(f)
    
    site_config = site_configs.get("freerideinvestor.com", {})
    if not site_config:
        print("❌ freerideinvestor.com not found in site_configs.json")
        return 1
    
    # Initialize deployer
    if not DEPLOYER_AVAILABLE:
        print("❌ SimpleWordPressDeployer not available")
        return 1
    
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    diagnostics = {}
    
    # Run diagnostics
    print("📋 Running diagnostics...")
    print()
    
    print("1️⃣  Checking error logs...")
    error_logs = check_error_logs(deployer)
    diagnostics["error_logs"] = error_logs
    if error_logs:
        print(f"   ✅ Found {len(error_logs)} error log(s)")
        for log in error_logs:
            print(f"      - {log['path']}: {len(log['last_lines'])} lines")
    else:
        print("   ⚠️  No error logs found")
    print()
    
    print("2️⃣  Checking wp-config.php debug settings...")
    wp_config = check_wp_config(deployer)
    diagnostics["wp_config"] = wp_config
    if wp_config:
        print("   ✅ Debug settings found:")
        for line in wp_config.split('\n')[:5]:
            if line.strip():
                print(f"      {line.strip()}")
    else:
        print("   ⚠️  No debug settings found")
    print()
    
    print("3️⃣  Checking PHP version...")
    php_version = check_php_version(deployer)
    diagnostics["php_version"] = php_version
    if php_version:
        print(f"   ✅ {php_version}")
    else:
        print("   ⚠️  Could not determine PHP version")
    print()
    
    print("4️⃣  Checking database connectivity...")
    db_check = check_database_connection(deployer)
    diagnostics["database"] = db_check
    if db_check:
        if "Success" in db_check or "OK" in db_check:
            print("   ✅ Database connection OK")
        else:
            print(f"   ⚠️  {db_check[:200]}")
    else:
        print("   ⚠️  Could not check database")
    print()
    
    print("5️⃣  Checking active plugins...")
    plugins = check_plugin_conflicts(deployer)
    diagnostics["plugins"] = plugins
    if plugins:
        try:
            import json
            plugin_list = json.loads(plugins)
            print(f"   ✅ Found {len(plugin_list)} active plugin(s)")
            for plugin in plugin_list[:5]:
                print(f"      - {plugin.get('name', 'Unknown')}")
        except:
            print(f"   ⚠️  {plugins[:200]}")
    else:
        print("   ⚠️  Could not list plugins")
    print()
    
    print("6️⃣  Checking active theme...")
    theme = check_theme_status(deployer)
    diagnostics["theme"] = theme
    if theme:
        try:
            import json
            theme_list = json.loads(theme)
            if theme_list:
                print(f"   ✅ Active theme: {theme_list[0].get('name', 'Unknown')}")
        except:
            print(f"   ⚠️  {theme[:200]}")
    else:
        print("   ⚠️  Could not check theme")
    print()
    
    print("7️⃣  Checking file permissions...")
    permissions = check_file_permissions(deployer)
    diagnostics["permissions"] = permissions
    if permissions:
        print(f"   ✅ Checked {len(permissions)} file(s)")
        for file, perm in permissions.items():
            print(f"      - {file}: {perm.split()[0] if perm.split() else 'unknown'}")
    else:
        print("   ⚠️  Could not check permissions")
    print()
    
    # Save diagnostics report
    report_path = Path(__file__).parent.parent / "docs" / "freerideinvestor_500_diagnostic.json"
    report_path.parent.mkdir(parents=True, exist_ok=True)
    
    with open(report_path, 'w', encoding='utf-8') as f:
        json.dump(diagnostics, f, indent=2)
    
    print("=" * 70)
    print("📊 DIAGNOSTIC SUMMARY")
    print("=" * 70)
    print(f"✅ Diagnostics complete")
    print(f"📄 Report saved: {report_path}")
    print()
    print("💡 Next Steps:")
    print("   1. Review error logs for specific error messages")
    print("   2. Check wp-config.php for misconfigurations")
    print("   3. Verify database connection is working")
    print("   4. Test disabling plugins one by one")
    print("   5. Check theme for syntax errors")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

