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
import json
from pathlib import Path

# Add main repo tools to path
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
if MAIN_REPO_TOOLS.exists():
    sys.path.insert(0, str(MAIN_REPO_TOOLS))

# Try to use SimpleWordPressDeployer (local, preferred)
DEPLOYMENT_TOOLS = Path(__file__).parent.parent / "ops" / "deployment"
if DEPLOYMENT_TOOLS.exists():
    sys.path.insert(0, str(DEPLOYMENT_TOOLS))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
    WordPressManager = SimpleWordPressDeployer
    DEPLOYER_AVAILABLE = True
except ImportError:
    # Fallback: Try main repo unified manager
    MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
    if MAIN_REPO_TOOLS.exists():
        sys.path.insert(0, str(MAIN_REPO_TOOLS))
    
    try:
        from unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod
        # Create adapter for compatibility
        class WordPressManagerAdapter:
            def __init__(self, site_key: str):
                self.manager = UnifiedWordPressManager(site_key)
                self.site_key = site_key
            
            def connect(self) -> bool:
                if hasattr(self.manager, 'deployer') and self.manager.deployer:
                    return self.manager.deployer.connect()
                return False
            
            def deploy_file(self, local_path: Path) -> bool:
                return self.manager.deploy_file(local_path, method=DeploymentMethod.SFTP)
            
            def disconnect(self):
                if hasattr(self.manager, 'deployer') and self.manager.deployer:
                    self.manager.deployer.disconnect()
        
        WordPressManager = WordPressManagerAdapter
        DEPLOYER_AVAILABLE = True
    except ImportError:
        print("❌ ERROR: No WordPress deployer found!")
        print("   Tried: simple_wordpress_deployer, unified_wordpress_manager")
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
        # Load site configs for SimpleWordPressDeployer
        config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
        site_configs = {}
        if config_path.exists():
            with open(config_path, 'r', encoding='utf-8') as f:
                site_configs = json.load(f)
        
        # Initialize manager
        if WordPressManager == SimpleWordPressDeployer:
            manager = WordPressManager(config["site_key"], site_configs)
        else:
            manager = WordPressManager(config["site_key"])
        
        # Connect to server
        print(f"📡 Connecting to {config['site_key']}...")
        if not manager.connect():
            print(f"❌ Failed to connect to {config['site_key']}")
            print("   Check credentials in configs/site_configs.json or .env")
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

