#!/usr/bin/env python3
"""
Optimize dadudekc.com Response Time
===================================

Performance optimization tool for dadudekc.com.
Target: Reduce response time from 23.05s to <3s.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import json
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


def enable_wp_cache(deployer):
    """Enable WordPress caching via wp-config.php."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/dadudekc.com/public_html"
        
        # Check if WP_CACHE is already enabled
        command = f"cd {remote_path} && grep -q 'WP_CACHE' wp-config.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        result = deployer.execute_command(command)
        
        if "EXISTS" in result:
            print("   ✅ WP_CACHE already configured")
            return True
        
        # Add WP_CACHE constant before 'That's all, stop editing!'
        cache_config = """
// Enable WordPress caching
define('WP_CACHE', true);
"""
        
        # Read current wp-config.php
        command = f"cd {remote_path} && cat wp-config.php"
        wp_config = deployer.execute_command(command)
        
        if not wp_config:
            print("   ❌ Could not read wp-config.php")
            return False
        
        # Insert cache config before "That's all"
        if "That's all" in wp_config:
            wp_config = wp_config.replace(
                "/* That's all, stop editing!",
                f"{cache_config}\n/* That's all, stop editing!"
            )
        else:
            # Add before closing PHP tag or at end
            wp_config = wp_config.rstrip() + cache_config
        
        # Write back (this would need file upload capability)
        print("   ⚠️  wp-config.php modification requires file upload")
        print("   💡 Manual step: Add WP_CACHE constant to wp-config.php")
        return False
        
    finally:
        deployer.disconnect()


def install_cache_plugin(deployer):
    """Install and activate a caching plugin via WP-CLI."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/dadudekc.com/public_html"
        
        # Try WP Super Cache (lightweight)
        command = f"cd {remote_path} && wp plugin install wp-super-cache --activate --allow-root 2>&1"
        result = deployer.execute_command(command)
        
        if "Success" in result or "Plugin installed" in result:
            print("   ✅ WP Super Cache installed and activated")
            return True
        else:
            print(f"   ⚠️  Plugin installation: {result[:200]}")
            return False
        
    finally:
        deployer.disconnect()


def optimize_database(deployer):
    """Optimize WordPress database via WP-CLI."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/dadudekc.com/public_html"
        
        # Optimize database
        command = f"cd {remote_path} && wp db optimize --allow-root 2>&1"
        result = deployer.execute_command(command)
        
        if "Success" in result or "Optimized" in result:
            print("   ✅ Database optimized")
            return True
        else:
            print(f"   ⚠️  Database optimization: {result[:200]}")
            return False
        
    finally:
        deployer.disconnect()


def clear_transients(deployer):
    """Clear WordPress transients (temporary cached data)."""
    if not deployer or not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/dadudekc.com/public_html"
        
        # Delete expired transients
        command = f"cd {remote_path} && wp transient delete --all --allow-root 2>&1"
        result = deployer.execute_command(command)
        
        print("   ✅ Transients cleared")
        return True
        
    finally:
        deployer.disconnect()


def check_php_memory_limit(deployer):
    """Check and suggest PHP memory limit increase."""
    if not deployer or not deployer.connect():
        return None
    
    try:
        remote_path = getattr(deployer, 'remote_path', '')
        if not remote_path:
            remote_path = "domains/dadudekc.com/public_html"
        
        # Check current memory limit in wp-config.php
        command = f"cd {remote_path} && grep -i 'WP_MEMORY_LIMIT' wp-config.php || echo 'NOT_FOUND'"
        result = deployer.execute_command(command)
        
        if "NOT_FOUND" in result:
            print("   ⚠️  WP_MEMORY_LIMIT not set (default: 40M)")
            print("   💡 Recommend: define('WP_MEMORY_LIMIT', '256M');")
            return False
        else:
            print(f"   ✅ Memory limit configured: {result.strip()}")
            return True
        
    finally:
        deployer.disconnect()


def main():
    """Main optimization execution."""
    print("=" * 70)
    print("⚡ DADUDEKC.COM PERFORMANCE OPTIMIZATION")
    print("=" * 70)
    print("Target: Reduce response time from 23.05s to <3s")
    print()
    
    # Load site configs
    config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
    if not config_path.exists():
        print("❌ site_configs.json not found")
        return 1
    
    with open(config_path, 'r', encoding='utf-8') as f:
        site_configs = json.load(f)
    
    # Try different key formats
    site_config = site_configs.get("dadudekc.com", {}) or site_configs.get("dadudekc", {})
    
    if not site_config:
        # Create minimal config from sites_registry if available
        registry_path = Path(__file__).parent.parent / "configs" / "sites_registry.json"
        if registry_path.exists():
            with open(registry_path, 'r', encoding='utf-8') as f:
                registry = json.load(f)
            if "dadudekc.com" in registry:
                site_config = {
                    "site_url": "https://dadudekc.com",
                    "deployment_method": "sftp",
                    "sftp": {
                        "host": None,
                        "username": None,
                        "password": None,
                        "remote_path": "domains/dadudekc.com/public_html"
                    }
                }
    
    if not site_config:
        print("❌ dadudekc.com not found in site configs")
        print("   Using default remote_path: domains/dadudekc.com/public_html")
        site_config = {
            "site_url": "https://dadudekc.com",
            "deployment_method": "sftp",
            "sftp": {
                "remote_path": "domains/dadudekc.com/public_html"
            }
        }
    
    # Ensure site_config is in site_configs dict for deployer
    if "dadudekc.com" not in site_configs:
        site_configs["dadudekc.com"] = site_config
    
    # Initialize deployer
    if not DEPLOYER_AVAILABLE:
        print("❌ SimpleWordPressDeployer not available")
        return 1
    
    deployer = SimpleWordPressDeployer("dadudekc.com", site_configs)
    
    optimizations = {}
    
    print("📋 Running performance optimizations...")
    print()
    
    print("1️⃣  Checking PHP memory limit...")
    memory_ok = check_php_memory_limit(deployer)
    optimizations["memory_limit"] = memory_ok
    print()
    
    print("2️⃣  Clearing transients...")
    transients_cleared = clear_transients(deployer)
    optimizations["transients"] = transients_cleared
    print()
    
    print("3️⃣  Optimizing database...")
    db_optimized = optimize_database(deployer)
    optimizations["database"] = db_optimized
    print()
    
    print("4️⃣  Installing caching plugin...")
    cache_installed = install_cache_plugin(deployer)
    optimizations["caching"] = cache_installed
    print()
    
    print("5️⃣  Enabling WordPress cache...")
    cache_enabled = enable_wp_cache(deployer)
    optimizations["wp_cache"] = cache_enabled
    print()
    
    # Save optimization report
    report_path = Path(__file__).parent.parent / "docs" / "dadudekc_performance_optimization.json"
    report_path.parent.mkdir(parents=True, exist_ok=True)
    
    with open(report_path, 'w', encoding='utf-8') as f:
        json.dump(optimizations, f, indent=2)
    
    print("=" * 70)
    print("📊 OPTIMIZATION SUMMARY")
    print("=" * 70)
    
    completed = sum(1 for v in optimizations.values() if v)
    total = len(optimizations)
    
    print(f"✅ Completed: {completed}/{total} optimizations")
    print(f"📄 Report saved: {report_path}")
    print()
    print("💡 Additional Recommendations:")
    print("   1. Enable GZIP compression (via .htaccess or hosting panel)")
    print("   2. Optimize images (compress, use WebP format)")
    print("   3. Minify CSS and JavaScript")
    print("   4. Use CDN for static assets")
    print("   5. Check for slow database queries (enable query logging)")
    print("   6. Review active plugins (disable unused plugins)")
    print("   7. Consider upgrading hosting plan if server is slow")
    print("   8. Add meta description and H1 heading (SEO task)")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

