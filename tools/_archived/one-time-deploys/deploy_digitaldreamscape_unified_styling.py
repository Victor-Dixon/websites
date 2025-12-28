#!/usr/bin/env python3
"""
Deploy Unified Subheader & Styling System to digitaldreamscape.site
====================================================================

Adds consistent subheader strip and unified visual system styling.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

# Add parent directory to path for imports
sys.path.insert(0, str(Path(__file__).parent.parent))

import json
from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer

def load_site_configs():
    """Load site configurations from config file."""
    config_path = Path(__file__).parent.parent / "configs" / "sites_registry.json"
    if config_path.exists():
        with open(config_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {}

def main():
    print("=" * 70)
    print("DEPLOYING UNIFIED SUBHEADER & STYLING SYSTEM")
    print("to digitaldreamscape.site")
    print("=" * 70)
    print()
    
    # Load site configs and initialize deployer
    site_configs = load_site_configs()
    site_key = "digitaldreamscape.site"
    
    if site_key not in site_configs:
        print(f"❌ Site config not found for {site_key}")
        return
    
    deployer = SimpleWordPressDeployer(site_key=site_key, site_configs=site_configs)
    if not deployer.connect():
        print("❌ Could not connect to server")
        return
    
    # Site configuration
    site_domain = "digitaldreamscape.site"
    
    # Read the unified styling fix
    fix_file = Path(__file__).parent.parent / "docs" / "digitaldreamscape" / "UNIFIED_SUBHEADER_FIX.php"
    
    if not fix_file.exists():
        print(f"❌ ERROR: Fix file not found: {fix_file}")
        return
    
    print(f"📄 Reading unified styling fix...")
    with open(fix_file, 'r', encoding='utf-8') as f:
        fix_content = f.read()
    
    print(f"   ✅ Fix content loaded ({len(fix_content)} bytes)")
    print()
    
    # Deploy to functions.php
    print(f"🔧 Deploying unified styling system to functions.php...")
    try:
        remote_base = site_configs[site_key].get('sftp', {}).get('remote_path', 
            f'domains/{site_key}/public_html')
        if not remote_base.startswith('/'):
            username = site_configs[site_key].get('sftp', {}).get('username', 'u996867598')
            remote_base = f"/home/{username}/{remote_base}"
        
        theme_path_remote = f"{remote_base}/wp-content/themes/digitaldreamscape"
        functions_file_remote = f"{theme_path_remote}/functions.php"
        
        # Read current functions.php
        print(f"   📄 Reading current functions.php...")
        command = f"cat {functions_file_remote}"
        current_functions = deployer.execute_command(command)
        if not current_functions:
            print("   ⚠️  Could not read functions.php, will append")
            current_functions = ""
        
        # Check if already added
        if 'digitaldreamscape_unified_subheader' in current_functions:
            print(f"   ⚠️  Unified styling system already exists - skipping")
        else:
            # Append the fix
            print(f"   ➕ Appending unified styling system...")
            updated_functions = current_functions
            if not updated_functions.endswith("\n"):
                updated_functions += "\n"
            updated_functions += "\n" + fix_content + "\n"
            
            # Write to temp file and deploy
            import tempfile
            with tempfile.NamedTemporaryFile(mode='w', suffix='.php', delete=False, encoding='utf-8') as tmp_file:
                tmp_file.write(updated_functions)
                tmp_path = Path(tmp_file.name)
            
            if deployer.deploy_file(tmp_path, functions_file_remote):
                print(f"   ✅ Unified styling system deployed successfully")
                tmp_path.unlink()  # Clean up temp file
            else:
                print(f"   ❌ Deployment failed")
                return
    except Exception as e:
        print(f"   ❌ Deployment failed: {e}")
        import traceback
        traceback.print_exc()
        return
    
    print()
    
    # Clear WordPress cache
    print(f"💬 Clearing WordPress cache...")
    try:
        deployer.clear_wordpress_cache(site_domain)
        print(f"   ✅ Cache clear attempted")
    except Exception as e:
        print(f"   ⚠️  Cache clear warning: {e}")
    
    print()
    print("=" * 70)
    print("✅ DEPLOYMENT COMPLETE!")
    print("=" * 70)
    print()
    print("📋 Next Steps:")
    print("   1. Visit https://digitaldreamscape.site/")
    print("   2. Visit https://digitaldreamscape.site/blog/")
    print("   3. Verify subheader strip appears on both pages")
    print("   4. Verify card styling is consistent")
    print("   5. Clear browser cache (Ctrl+F5) if needed")
    print()
    
    deployer.disconnect()

if __name__ == "__main__":
    main()

