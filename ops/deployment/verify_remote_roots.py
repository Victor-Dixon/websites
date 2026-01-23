#!/usr/bin/env python3
"""Verify all sites have remote_root configured."""

import yaml
from pathlib import Path

registry_path = Path(__file__).parent / "sites.yml"

with open(registry_path, 'r') as f:
    data = yaml.safe_load(f)

sites = data.get('sites', {})
print(f"✅ Registry loaded: {len(sites)} sites\n")
print("Remote roots:")
for site_key, site_config in sites.items():
    remote_root = site_config.get('remote_root', 'MISSING')
    status = "✅" if remote_root != 'MISSING' else "❌"
    print(f"  {status} {site_key}: {remote_root}")

missing = [k for k, v in sites.items() if not v.get('remote_root')]
if missing:
    print(f"\n❌ Missing remote_root: {', '.join(missing)}")
    exit(1)
else:
    print("\n✅ All sites have remote_root configured")
