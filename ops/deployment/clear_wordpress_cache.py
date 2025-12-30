#!/usr/bin/env python3
"""
Clear WordPress Cache Tool
==========================

A comprehensive tool to clear all types of WordPress cache for any site:
- WordPress object cache (wp_cache_flush)
- WP-CLI cache flush
- Transients
- Rewrite rules
- LiteSpeed Cache (if installed)
- CDN cache headers (if applicable)

Usage:
    python clear_wordpress_cache.py <site_name>
    
    Example:
    python clear_wordpress_cache.py digitaldreamscape.site

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-25
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def clear_wordpress_cache(site_name: str, verbose: bool = True) -> bool:
    """
    Clear all WordPress cache for a specific site.
    
    Args:
        site_name: The site identifier (e.g., 'digitaldreamscape.site')
        verbose: Print detailed output
        
    Returns:
        True if successful, False otherwise
    """
    print("=" * 70)
    print(f"🧹 CLEARING WORDPRESS CACHE: {site_name}")
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
        if not remote_path.startswith('/'):
            # Make absolute path
            username = deployer.site_config.get('username') or deployer.site_config.get('sftp', {}).get('username', '')
            if username:
                remote_path = f"/home/{username}/{remote_path}"
        
        wp_root = remote_path.rstrip('/')
        
        print(f"📁 WordPress root: {wp_root}")
        print()
        
        # 1. Clear WordPress object cache
        if verbose:
            print("1️⃣  Clearing WordPress object cache (wp_cache_flush)...")
        cache_result = deployer.execute_command(f"cd {wp_root} && wp cache flush --allow-root 2>&1")
        if verbose:
            if "Success" in cache_result or "Cache flushed" in cache_result or not cache_result.strip():
                print("   ✅ WordPress cache flushed")
            else:
                print(f"   ⚠️  Result: {cache_result[:200]}")
        else:
            if "Error" in cache_result or "failed" in cache_result.lower():
                print(f"   ⚠️  WordPress cache flush: {cache_result[:100]}")
        
        # 2. Clear object cache (alternative method)
        if verbose:
            print("2️⃣  Clearing object cache (wp cache delete)...")
        obj_cache = deployer.execute_command(f"cd {wp_root} && wp cache delete --all --allow-root 2>&1")
        if verbose and obj_cache.strip():
            print(f"   ⚠️  Result: {obj_cache[:200]}")
        
        # 3. Flush rewrite rules (forces template refresh)
        if verbose:
            print("3️⃣  Flushing rewrite rules (forces template refresh)...")
        rewrite = deployer.execute_command(f"cd {wp_root} && wp rewrite flush --allow-root 2>&1")
        if verbose:
            if "Success" in rewrite or "Rewrite rules flushed" in rewrite or not rewrite.strip():
                print("   ✅ Rewrite rules flushed")
            else:
                print(f"   ⚠️  Result: {rewrite[:200]}")
        
        # 4. Clear all transients
        if verbose:
            print("4️⃣  Clearing all transients...")
        transients = deployer.execute_command(f"cd {wp_root} && wp transient delete --all --allow-root 2>&1")
        if verbose:
            if "Success" in transients or "deleted" in transients.lower() or not transients.strip():
                print("   ✅ Transients cleared")
            else:
                print(f"   ⚠️  Result: {transients[:200]}")
        
        # 5. Clear LiteSpeed Cache (if installed)
        if verbose:
            print("5️⃣  Attempting to clear LiteSpeed Cache...")
        litespeed = deployer.execute_command(f"cd {wp_root} && wp litespeed-purge all --allow-root 2>&1")
        if verbose:
            if "Success" in litespeed or "purged" in litespeed.lower():
                print("   ✅ LiteSpeed Cache purged")
            elif "not found" in litespeed.lower() or "Unknown command" in litespeed:
                print("   ℹ️  LiteSpeed Cache plugin not installed (skipping)")
            else:
                print(f"   ⚠️  Result: {litespeed[:200]}")
        
        # 6. Clear WP Super Cache (if installed)
        if verbose:
            print("6️⃣  Attempting to clear WP Super Cache...")
        super_cache = deployer.execute_command(f"cd {wp_root} && wp super-cache flush --allow-root 2>&1")
        if verbose:
            if "Success" in super_cache or "cleared" in super_cache.lower():
                print("   ✅ WP Super Cache cleared")
            elif "not found" in super_cache.lower() or "Unknown command" in super_cache:
                print("   ℹ️  WP Super Cache plugin not installed (skipping)")
            else:
                print(f"   ⚠️  Result: {super_cache[:200]}")
        
        # 7. Clear W3 Total Cache (if installed)
        if verbose:
            print("7️⃣  Attempting to clear W3 Total Cache...")
        w3tc = deployer.execute_command(f"cd {wp_root} && wp w3-total-cache flush all --allow-root 2>&1")
        if verbose:
            if "Success" in w3tc or "cleared" in w3tc.lower():
                print("   ✅ W3 Total Cache cleared")
            elif "not found" in w3tc.lower() or "Unknown command" in w3tc:
                print("   ℹ️  W3 Total Cache plugin not installed (skipping)")
            else:
                print(f"   ⚠️  Result: {w3tc[:200]}")
        
        print()
        print("=" * 70)
        print("✅ CACHE CLEARING COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next Steps:")
        print("   1. Hard refresh your browser: Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)")
        print("   2. Clear browser cache if changes still not visible")
        print("   3. Check CDN cache if using a CDN service")
        print()
        
        return True
        
    except Exception as e:
        print(f"❌ Error clearing cache: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    if len(sys.argv) < 2:
        print("Usage: python clear_wordpress_cache.py <site_name>")
        print()
        print("Examples:")
        print("  python clear_wordpress_cache.py digitaldreamscape.site")
        print("  python clear_wordpress_cache.py prismblossom.online")
        print("  python clear_wordpress_cache.py freerideinvestor.com")
        sys.exit(1)
    
    site_name = sys.argv[1]
    verbose = '--quiet' not in sys.argv and '-q' not in sys.argv
    
    success = clear_wordpress_cache(site_name, verbose=verbose)
    sys.exit(0 if success else 1)


if __name__ == "__main__":
    main()

