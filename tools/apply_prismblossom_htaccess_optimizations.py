#!/usr/bin/env python3
"""
Apply .htaccess Optimizations to prismblossom.online
====================================================

Applies Apache performance optimizations to .htaccess.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def apply_htaccess_optimizations():
    """Apply .htaccess optimizations."""
    site_name = "prismblossom.online"
    
    print("=" * 70)
    print(f"⚙️  APPLYING .HTACCESS OPTIMIZATIONS: {site_name}")
    print("=" * 70)
    print()
    
    # Load site configs
    site_configs = load_site_configs()
    
    if site_name not in site_configs:
        print(f"❌ {site_name} not found in site_configs.json")
        return False
    
    # Ensure site_config is in site_configs dict for deployer
    if site_name not in site_configs:
        site_configs[site_name] = site_configs.get(site_name, {})
    
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
        htaccess_path = f"{remote_path}/.htaccess"
        
        # Read optimization content
        optimizations_dir = Path(__file__).parent.parent / "websites" / site_name / "optimizations"
        htaccess_opt_file = optimizations_dir / "htaccess-optimizations.txt"
        
        if not htaccess_opt_file.exists():
            print(f"❌ Optimization file not found: {htaccess_opt_file}")
            return False
        
        htaccess_optimizations = htaccess_opt_file.read_text(encoding='utf-8')
        
        # Check if .htaccess exists
        print(f"📖 Reading {htaccess_path}...")
        check_cmd = f"test -f {htaccess_path} && echo 'exists' || echo 'not found'"
        check_result = deployer.execute_command(check_cmd)
        
        if 'not found' in check_result:
            print("   ⚠️  .htaccess not found, creating new file...")
            htaccess_content = ""
        else:
            read_cmd = f"cat {htaccess_path}"
            htaccess_content = deployer.execute_command(read_cmd)
            
            if not htaccess_content:
                htaccess_content = ""
        
        # Check if optimizations already exist
        if 'WordPress Performance Optimizations - Added by Agent-7' in htaccess_content:
            print("⚠️  Performance optimizations may already exist")
            if 'mod_deflate.c' in htaccess_content and 'mod_expires.c' in htaccess_content:
                print("   ✅ All optimizations already applied")
                return True
        
        # Add optimizations at the end
        if htaccess_content.strip():
            new_content = htaccess_content.rstrip() + '\n\n' + htaccess_optimizations
        else:
            new_content = htaccess_optimizations
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_htaccess_optimized.txt"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying optimized .htaccess...")
        success = deployer.deploy_file(local_file, htaccess_path)
        
        if success:
            print(f"   ✅ .htaccess optimizations deployed!")
            print()
            print("✅ .htaccess optimizations applied successfully!")
            print()
            print("💡 Optimizations applied:")
            print("   - GZIP compression enabled")
            print("   - Browser caching configured")
            print("   - Cache-Control headers set")
            return True
        else:
            print("   ❌ Failed to deploy optimized file")
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
    success = apply_htaccess_optimizations()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


