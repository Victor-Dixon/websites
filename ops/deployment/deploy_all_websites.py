#!/usr/bin/env python3
"""
Deploy All Websites - Direct Hosting Connection
===============================================

Deploys website fixes directly to hosting via SFTP/SSH using WordPressManager.
Uses the same deployment system that deployed dadudekc.com, weareswarm.online, freerideinvestor.com

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-30
"""

import sys
from pathlib import Path

# Add main repo tools to path
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
if MAIN_REPO_TOOLS.exists():
    sys.path.insert(0, str(MAIN_REPO_TOOLS))

try:
    from wordpress_manager import WordPressManager
except ImportError:
    print("❌ ERROR: WordPressManager not found!")
    print(f"   Expected at: {MAIN_REPO_TOOLS}/wordpress_manager.py")
    sys.exit(1)

# Load .env file for credentials
try:
    from dotenv import load_dotenv, dotenv_values
    env_vars = dotenv_values("D:/Agent_Cellphone_V2_Repository/.env")
    import os
    for key, value in env_vars.items():
        if value and key not in os.environ:
            os.environ[key] = value
    load_dotenv("D:/Agent_Cellphone_V2_Repository/.env")
except ImportError:
    pass  # dotenv not installed
except Exception:
    pass  # .env file not found


# Site configuration mapping
SITE_CONFIGS = {
    "FreeRideInvestor": {
        "site_key": "freerideinvestor",
        "files": [
            "functions.php",
            "css/styles/base/_typography.css",
            "css/styles/base/_variables.css"
        ]
    },
    "prismblossom.online": {
        "site_key": "prismblossom",
        "files": [
            "wordpress-theme/prismblossom/functions.php",
            "wordpress-theme/prismblossom/page-carmyn.php"
        ]
    },
    "southwestsecret.com": {
        "site_key": "southwestsecret",
        "files": [
            "css/style.css",
            "wordpress-theme/southwestsecret/functions.php"
        ]
    }
}


def deploy_site(site_name: str, config: dict) -> bool:
    """Deploy all files for a site."""
    print(f"\n{'='*60}")
    print(f"🌐 DEPLOYING: {site_name}")
    print(f"{'='*60}\n")
    
    try:
        manager = WordPressManager(config["site_key"])
        
        # Connect to server
        print(f"📡 Connecting to {config['site_key']}...")
        if not manager.connect():
            print(f"❌ Failed to connect to {config['site_key']}")
            print("   Check credentials in .deploy_credentials/sites.json")
            return False
        print("✅ Connected!\n")
        
        # Deploy each file
        base_path = Path("D:/websites") / site_name
        success_count = 0
        fail_count = 0
        
        for file_path in config["files"]:
            local_path = base_path / file_path
            if not local_path.exists():
                print(f"❌ File not found: {local_path}")
                fail_count += 1
                continue
            
            print(f"📤 Deploying: {file_path}...")
            if manager.deploy_file(local_path):
                print(f"✅ Deployed: {file_path}")
                success_count += 1
            else:
                print(f"❌ Failed: {file_path}")
                fail_count += 1
        
        # Disconnect
        manager.disconnect()
        
        print(f"\n📊 Summary for {site_name}:")
        print(f"   ✅ Succeeded: {success_count}")
        print(f"   ❌ Failed: {fail_count}")
        
        return fail_count == 0
        
    except ValueError as e:
        print(f"❌ Site configuration error: {e}")
        print(f"   Site key '{config['site_key']}' not found in WordPressManager")
        return False
    except Exception as e:
        print(f"❌ Error deploying {site_name}: {e}")
        return False


def main():
    """Deploy all websites."""
    print("\n" + "="*60)
    print("🚀 WEBSITE DEPLOYMENT - DIRECT HOSTING CONNECTION")
    print("="*60)
    
    results = {}
    
    for site_name, config in SITE_CONFIGS.items():
        results[site_name] = deploy_site(site_name, config)
    
    # Summary
    print("\n" + "="*60)
    print("📊 DEPLOYMENT SUMMARY")
    print("="*60)
    
    for site_name, success in results.items():
        status = "✅ SUCCESS" if success else "❌ FAILED"
        print(f"   {status}: {site_name}")
    
    all_success = all(results.values())
    
    if all_success:
        print("\n✅ All websites deployed successfully!")
        print("\n💡 Next Steps:")
        print("   1. Clear WordPress cache")
        print("   2. Clear browser cache")
        print("   3. Verify fixes on live sites")
        print("   4. Run: python tools/verify_website_fixes.py")
    else:
        print("\n⚠️  Some deployments failed. Check errors above.")
    
    return 0 if all_success else 1


if __name__ == '__main__':
    exit(main())

