#!/usr/bin/env python3
"""
Test All Website Deployers
===========================

Tests all deployment scripts to ensure they work correctly.
Checks connectivity, configuration, and deployment readiness.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-23
"""

import sys
import json
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

# Try SimpleWordPressDeployer
try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    SIMPLE_DEPLOYER_AVAILABLE = True
except ImportError:
    SIMPLE_DEPLOYER_AVAILABLE = False
    print("⚠️  SimpleWordPressDeployer not available")

# Try WordPressManager
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
WORDPRESS_MANAGER_AVAILABLE = False
if MAIN_REPO_TOOLS.exists():
    sys.path.insert(0, str(MAIN_REPO_TOOLS))
    try:
        from wordpress_manager import WordPressManager
        WORDPRESS_MANAGER_AVAILABLE = True
    except ImportError:
        pass

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
    pass
except Exception:
    pass


def load_site_registry():
    """Load site registry."""
    registry_path = Path("D:/websites/configs/sites_registry.json")
    if registry_path.exists():
        with open(registry_path, 'r') as f:
            return json.load(f)
    return {}


def test_deployment_method(site_domain: str, site_config: dict) -> dict:
    """Test deployment method for a site."""
    result = {
        'site': site_domain,
        'deployment_method': site_config.get('deployment_method', 'unknown'),
        'has_credentials': False,
        'can_connect': False,
        'alternative_method': None,
        'error': None
    }
    
    try:
        # Always try SFTP first (works with Hostinger env vars)
        if SIMPLE_DEPLOYER_AVAILABLE:
            site_configs = load_site_configs()
            if site_configs:
                try:
                    deployer = SimpleWordPressDeployer(site_domain, site_configs)
                    result['has_credentials'] = deployer.site_config is not None
                    if result['has_credentials']:
                        # Try to connect (don't actually deploy)
                        result['can_connect'] = deployer.connect()
                        if result['can_connect']:
                            result['deployment_method'] = 'sftp'
                            result['alternative_method'] = None
                            if deployer.sftp:
                                deployer.disconnect()
                            return result
                except Exception as e:
                    pass  # Try REST API fallback
        
        # If SFTP doesn't work, check REST API
        deployment_method = site_config.get('deployment_method', 'rest_api')
        if deployment_method == 'rest_api':
            rest_api = site_config.get('rest_api', {})
            username = rest_api.get('username', '')
            app_password = rest_api.get('app_password', '')
            
            if username and app_password and app_password != 'REPLACE_WITH_APPLICATION_PASSWORD':
                result['has_credentials'] = True
                result['deployment_method'] = 'rest_api'
            else:
                result['has_credentials'] = False
                result['error'] = 'REST API credentials not configured. SFTP fallback failed.'
                result['alternative_method'] = 'sftp (via Hostinger env vars)'
        else:
            result['error'] = 'SFTP connection failed and no alternative method configured'
            
    except Exception as e:
        result['error'] = str(e)
    
    return result


def main():
    """Test all deployment methods."""
    print("\n" + "="*60)
    print("🧪 TESTING ALL WEBSITE DEPLOYERS")
    print("="*60 + "\n")
    
    # Load configurations
    site_configs_path = Path("D:/websites/configs/site_configs.json")
    site_registry_path = Path("D:/websites/configs/sites_registry.json")
    
    if not site_configs_path.exists():
        print("❌ site_configs.json not found!")
        return 1
    
    with open(site_configs_path, 'r') as f:
        site_configs = json.load(f)
    
    site_registry = {}
    if site_registry_path.exists():
        with open(site_registry_path, 'r') as f:
            site_registry = json.load(f)
    
    # Test each site
    results = []
    for site_domain, site_config in site_configs.items():
        print(f"Testing: {site_domain}...")
        result = test_deployment_method(site_domain, site_config)
        results.append(result)
        
        status = "✅" if result['has_credentials'] and (result['can_connect'] or result['deployment_method'] == 'rest_api') else "❌"
        print(f"  {status} Method: {result['deployment_method']}")
        print(f"  {'  ✅' if result['has_credentials'] else '  ❌'} Credentials: {'Configured' if result['has_credentials'] else 'Missing'}")
        if result['can_connect']:
            print(f"  ✅ Connection: Successful")
        if result['error']:
            print(f"  ⚠️  Error: {result['error']}")
        print()
    
    # Summary
    print("="*60)
    print("📊 DEPLOYMENT TEST SUMMARY")
    print("="*60)
    
    working = sum(1 for r in results if r['has_credentials'] and (r['can_connect'] or r['deployment_method'] == 'rest_api'))
    total = len(results)
    
    print(f"\n✅ Working: {working}/{total}")
    print(f"❌ Needs Configuration: {total - working}/{total}\n")
    
    for result in results:
        status = "✅" if result['has_credentials'] and (result['can_connect'] or result['deployment_method'] == 'rest_api') else "❌"
        print(f"{status} {result['site']}: {result['deployment_method']}")
        if result['error']:
            print(f"   ⚠️  {result['error']}")
    
    return 0 if working == total else 1


if __name__ == '__main__':
    exit(main())
