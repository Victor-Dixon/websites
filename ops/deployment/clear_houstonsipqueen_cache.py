#!/usr/bin/env python3
"""
Clear Houston Sip Queen Cache
=============================

Clears all caches for houstonsipqueen.com.

Usage:
    python clear_houstonsipqueen_cache.py
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_KEY = "houstonsipqueen.com"


def clear_all_caches(deployer):
    """Clear all WordPress caches."""
    print(f"\n🧹 Clearing all caches...")
    
    # Clear WordPress cache
    print(f"\n1️⃣ Clearing WordPress cache...")
    command1 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp cache flush --allow-root"
    result1 = deployer.execute_command(command1)
    print(f"✅ WordPress cache: {result1.strip()}")
    
    # Clear object cache
    print(f"\n2️⃣ Clearing object cache...")
    command2 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp cache delete --all --allow-root 2>&1 || echo 'No object cache'"
    result2 = deployer.execute_command(command2)
    print(f"✅ Object cache: {result2.strip()}")
    
    # Clear transients
    print(f"\n3️⃣ Clearing transients...")
    command3 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp transient delete --all --allow-root 2>&1 || echo 'No transients'"
    result3 = deployer.execute_command(command3)
    print(f"✅ Transients: {result3.strip()}")
    
    # Flush rewrite rules (helps with template recognition)
    print(f"\n4️⃣ Flushing rewrite rules...")
    command4 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp rewrite flush --allow-root"
    result4 = deployer.execute_command(command4)
    print(f"✅ Rewrite rules: {result4.strip()}")
    
    # Clear any plugin caches (if common caching plugins exist)
    print(f"\n5️⃣ Clearing plugin caches...")
    plugins_to_clear = ['w3-total-cache', 'wp-super-cache', 'wp-rocket', 'litespeed-cache']
    for plugin in plugins_to_clear:
        command = f"cd /home/*/domains/houstonsipqueen.com/public_html && wp {plugin} flush --allow-root 2>&1 || echo ''"
        result = deployer.execute_command(command)
        if result.strip() and 'not found' not in result.lower():
            print(f"   ✅ {plugin}: {result.strip()}")
    
    return True


def main():
    """Main function."""
    print(f"\n{'='*70}")
    print(f"🧹 CLEARING CACHE: {SITE_KEY}")
    print(f"{'='*70}\n")
    
    # Load site configurations
    site_configs = load_site_configs()
    
    if SITE_KEY not in site_configs:
        print(f"❌ Site '{SITE_KEY}' not found in configuration")
        return False
    
    # Initialize deployer
    try:
        deployer = SimpleWordPressDeployer(SITE_KEY, site_configs)
    except ValueError as e:
        print(f"❌ {e}")
        return False
    
    # Connect to server
    print(f"🔌 Connecting to server...")
    if not deployer.connect():
        print(f"❌ Failed to connect to {SITE_KEY}")
        return False
    
    # Clear all caches
    success = clear_all_caches(deployer)
    
    # Disconnect
    deployer.disconnect()
    
    if success:
        print(f"\n✅ All caches cleared!")
        print(f"🌐 Visit https://houstonsipqueen.com and do a hard refresh (Ctrl+F5) to see changes")
    else:
        print(f"\n⚠️  Cache clearing may have failed.")
    
    return success


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)


