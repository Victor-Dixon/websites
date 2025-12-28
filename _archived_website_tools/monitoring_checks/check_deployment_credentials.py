#!/usr/bin/env python3
"""Check deployment credentials availability."""

import os
import json
import re
from pathlib import Path

REPO_ROOT = Path(__file__).resolve().parents[1]


def _normalize_site_key(site_key: str) -> str:
    token = re.sub(r"[^A-Za-z0-9]+", "_", site_key).upper().strip("_")
    return token or "SITE"


def _load_dotenv_if_available(path: Path) -> None:
    try:
        from dotenv import load_dotenv  # type: ignore
    except Exception:
        return
    if path.exists():
        try:
            load_dotenv(path, override=False)
        except Exception:
            return

print("=" * 70)
print("🔍 DEPLOYMENT CREDENTIALS DIAGNOSTIC")
print("=" * 70)
print()

# Check .env file
env_path = REPO_ROOT / ".env"
print(f"1. Environment Variables (.env file):")
print(f"   Location: {env_path}")
print(f"   Exists: {'✅ Yes' if env_path.exists() else '❌ No'}")
if env_path.exists():
    _load_dotenv_if_available(env_path)
    host = os.getenv("HOSTINGER_HOST")
    user = os.getenv("HOSTINGER_USER")
    password = os.getenv("HOSTINGER_PASS")
    port = os.getenv("HOSTINGER_PORT", "65002")
    print(f"   HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
    print(f"   HOSTINGER_USER: {'✅ Set' if user else '❌ Missing'}")
    print(f"   HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
    print(f"   HOSTINGER_PORT: {port}")
else:
    print("   ⚠️  .env file not found - Hostinger credentials cannot be loaded")
print()

# Check sites.json
sites_json_path = REPO_ROOT / ".deploy_credentials" / "sites.json"
print(f"2. sites.json (WordPressManager format):")
print(f"   Location: {sites_json_path}")
print(f"   Exists: {'✅ Yes' if sites_json_path.exists() else '❌ No'}")
if sites_json_path.exists():
    try:
        with open(sites_json_path, 'r') as f:
            sites_config = json.load(f)
        print(f"   Sites configured: {len(sites_config)}")
        for site_key in list(sites_config.keys())[:3]:
            site = sites_config[site_key]
            has_host = bool(site.get('host'))
            has_user = bool(site.get('username'))
            has_pass = bool(site.get('password'))
            print(f"   - {site_key}: host={'✅' if has_host else '❌'}, user={'✅' if has_user else '❌'}, pass={'✅' if has_pass else '❌'}")
    except Exception as e:
        print(f"   ❌ Error reading: {e}")
print()

# Check site_configs.json
config_path = Path(os.getenv("SITE_CONFIGS_PATH", str(REPO_ROOT / "configs" / "site_configs.json")))
print(f"3. site_configs.json:")
print(f"   Location: {config_path}")
print(f"   Exists: {'✅ Yes' if config_path.exists() else '❌ No'}")
if config_path.exists():
    try:
        with open(config_path, 'r') as f:
            site_configs = json.load(f)
        print(f"   Sites configured: {len(site_configs)}")
        # Check freerideinvestor and prismblossom specifically
        for site_key in ['ariajet.site', 'freerideinvestor.com', 'prismblossom.online']:
            if site_key in site_configs:
                site = site_configs[site_key]
                sftp = site.get('sftp', {})
                has_host = bool(sftp.get('host'))
                has_user = bool(sftp.get('username'))
                has_pass = bool(sftp.get('password'))
                print(f"   - {site_key}:")
                print(f"     host: {'✅ Set' if has_host else '❌ Missing'}")
                print(f"     username: {'✅ Set' if has_user else '❌ Missing'}")
                print(f"     password: {'✅ Set' if has_pass else '❌ Missing'}")
                norm = _normalize_site_key(site_key)
                print(f"     env override: {norm}_SFTP_HOST / {norm}_SFTP_USER / {norm}_SFTP_PASS / {norm}_SFTP_PORT")
    except Exception as e:
        print(f"   ❌ Error reading: {e}")
print()

# Summary
print("=" * 70)
print("📊 SUMMARY")
print("=" * 70)
env_creds = all([os.getenv("HOSTINGER_HOST"), os.getenv("HOSTINGER_USER"), os.getenv("HOSTINGER_PASS")])
sites_json_exists = sites_json_path.exists()
config_json_exists = config_path.exists()

if env_creds:
    print("✅ Hostinger environment variables are set - deployment should work")
elif sites_json_exists:
    print("⚠️  Hostinger env vars missing, but sites.json exists - may work if it has credentials")
elif config_json_exists:
    print("⚠️  Hostinger env vars missing, sites.json missing, but site_configs.json exists")
    print("   ⚠️  However, site_configs.json shows empty SFTP credentials for freerideinvestor/prismblossom")
else:
    print("❌ No credential sources found - deployment will fail")

