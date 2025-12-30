#!/usr/bin/env python3
"""
Sync Site Credentials - Merge deploy credentials into config files
================================================================

Merges credentials from:
- .deploy_credentials/sites.json (SFTP credentials)
- .deploy_credentials/blogging_api.json (REST API credentials)

Into:
- configs/site_configs.json (deployment configs)
- configs/sites_registry.json (site registry - optional)
"""

import json
from pathlib import Path
from typing import Dict, Any

# Paths
REPO_ROOT = Path(r"D:\Agent_Cellphone_V2_Repository")
WEBSITES_ROOT = Path(r"D:\websites")

SITES_JSON = REPO_ROOT / ".deploy_credentials" / "sites.json"
BLOGGING_API_JSON = REPO_ROOT / ".deploy_credentials" / "blogging_api.json"
SITE_CONFIGS_JSON = WEBSITES_ROOT / "configs" / "site_configs.json"
SITES_REGISTRY_JSON = WEBSITES_ROOT / "configs" / "sites_registry.json"


def normalize_domain(domain: str) -> str:
    """Normalize domain name for matching."""
    return domain.lower().replace("www.", "").strip()


def load_json(file_path: Path) -> Dict[str, Any]:
    """Load JSON file."""
    if not file_path.exists():
        print(f"⚠️  File not found: {file_path}")
        return {}
    with open(file_path, 'r', encoding='utf-8') as f:
        return json.load(f)


def save_json(file_path: Path, data: Dict[str, Any]) -> None:
    """Save JSON file with pretty formatting."""
    file_path.parent.mkdir(parents=True, exist_ok=True)
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
    print(f"✅ Saved: {file_path}")


def sync_site_configs():
    """Sync credentials into site_configs.json."""
    print("🔄 Syncing site_configs.json...")
    
    sites_data = load_json(SITES_JSON)
    blogging_api_data = load_json(BLOGGING_API_JSON)
    site_configs = load_json(SITE_CONFIGS_JSON)
    
    updates = 0
    
    # Create domain mapping (handle variations like "freerideinvestor" vs "freerideinvestor.com")
    domain_map = {}
    for key, value in sites_data.items():
        normalized = normalize_domain(key)
        domain_map[normalized] = key
        # Also map with .com if site_url exists
        if 'site_url' in value:
            url_domain = normalize_domain(value['site_url'].replace('https://', '').replace('http://', ''))
            domain_map[url_domain] = key
    
    for api_key, api_value in blogging_api_data.items():
        normalized_api = normalize_domain(api_key)
        
        # Find matching site in site_configs by exact domain match
        for config_key, config_value in site_configs.items():
            normalized_config = normalize_domain(config_key)
            
            # Match by normalized domain (exact match only)
            if normalized_api == normalized_config:
                # Update REST API credentials
                if 'rest_api' in config_value:
                    if api_value.get('username'):
                        if config_value['rest_api'].get('username') in ['', 'REPLACE_WITH_WORDPRESS_USERNAME']:
                            config_value['rest_api']['username'] = api_value['username']
                            updates += 1
                    
                    if api_value.get('app_password'):
                        if config_value['rest_api'].get('app_password') in ['', 'REPLACE_WITH_APPLICATION_PASSWORD']:
                            config_value['rest_api']['app_password'] = api_value['app_password']
                            updates += 1
                
                # Update SFTP credentials from sites.json
                sites_key = domain_map.get(normalized_api, api_key)
                if sites_key in sites_data:
                    sftp_data = sites_data[sites_key]
                    if 'sftp' in config_value:
                        if sftp_data.get('host'):
                            if config_value['sftp'].get('host') in [None, '', 'null']:
                                config_value['sftp']['host'] = sftp_data['host']
                                updates += 1
                        
                        if sftp_data.get('username'):
                            if config_value['sftp'].get('username') in [None, '', 'null']:
                                config_value['sftp']['username'] = sftp_data['username']
                                updates += 1
                        
                        if sftp_data.get('password'):
                            if config_value['sftp'].get('password') in [None, '', 'null']:
                                config_value['sftp']['password'] = sftp_data['password']
                                updates += 1
                        
                        if sftp_data.get('port'):
                            if 'port' not in config_value['sftp']:
                                config_value['sftp']['port'] = sftp_data['port']
                                updates += 1
                        
                        if sftp_data.get('remote_path'):
                            if not config_value['sftp'].get('remote_path') or config_value['sftp']['remote_path'] == 'null':
                                config_value['sftp']['remote_path'] = sftp_data['remote_path']
                                updates += 1
    
    # Also sync from sites.json directly for sites that might not be in blogging_api.json
    for sites_key, sites_value in sites_data.items():
        normalized_sites = normalize_domain(sites_key)
        
        for config_key, config_value in site_configs.items():
            normalized_config = normalize_domain(config_key)
            
            if normalized_sites == normalized_config:
                # Update SFTP if not already set
                if 'sftp' in config_value:
                    if sites_value.get('host') and config_value['sftp'].get('host') in [None, '', 'null']:
                        config_value['sftp']['host'] = sites_value['host']
                        updates += 1
                    
                    if sites_value.get('username') and config_value['sftp'].get('username') in [None, '', 'null']:
                        config_value['sftp']['username'] = sites_value['username']
                        updates += 1
                    
                    if sites_value.get('password') and config_value['sftp'].get('password') in [None, '', 'null']:
                        config_value['sftp']['password'] = sites_value['password']
                        updates += 1
                    
                    if sites_value.get('port') and 'port' not in config_value['sftp']:
                        config_value['sftp']['port'] = sites_value['port']
                        updates += 1
                    
                    if sites_value.get('remote_path') and not config_value['sftp'].get('remote_path'):
                        config_value['sftp']['remote_path'] = sites_value['remote_path']
                        updates += 1
    
    save_json(SITE_CONFIGS_JSON, site_configs)
    print(f"✅ Updated {updates} credential fields in site_configs.json")


def sync_sites_registry():
    """Optionally sync additional info into sites_registry.json."""
    print("🔄 Syncing sites_registry.json (optional fields)...")
    
    blogging_api_data = load_json(BLOGGING_API_JSON)
    sites_registry = load_json(SITES_REGISTRY_JSON)
    
    updates = 0
    
    for api_key, api_value in blogging_api_data.items():
        normalized_api = normalize_domain(api_key)
        
        for registry_key, registry_value in sites_registry.items():
            normalized_registry = normalize_domain(registry_key)
            
            if normalized_api == normalized_registry:
                # Add purpose if not present
                if api_value.get('purpose') and 'purpose' not in registry_value:
                    registry_value['purpose'] = api_value['purpose']
                    updates += 1
                
                # Add categories if not present
                if api_value.get('categories') and 'categories' not in registry_value:
                    registry_value['categories'] = api_value['categories']
                    updates += 1
                
                # Add default_tags if not present
                if api_value.get('default_tags') and 'default_tags' not in registry_value:
                    registry_value['default_tags'] = api_value['default_tags']
                    updates += 1
    
    if updates > 0:
        save_json(SITES_REGISTRY_JSON, sites_registry)
        print(f"✅ Updated {updates} fields in sites_registry.json")
    else:
        print("ℹ️  No updates needed for sites_registry.json")


def main():
    """Main execution."""
    print("🚀 Starting credential sync...")
    print(f"   Source: {SITES_JSON}")
    print(f"   Source: {BLOGGING_API_JSON}")
    print(f"   Target: {SITE_CONFIGS_JSON}")
    print(f"   Target: {SITES_REGISTRY_JSON}")
    print()
    
    sync_site_configs()
    print()
    sync_sites_registry()
    print()
    print("✅ Credential sync complete!")


if __name__ == "__main__":
    main()

