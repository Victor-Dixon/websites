#!/usr/bin/env python3
"""Test site registry loading."""

import yaml
from pathlib import Path

registry_path = Path(__file__).parent / "sites.yml"

if registry_path.exists():
    with open(registry_path, 'r') as f:
        data = yaml.safe_load(f)
    sites = data.get('sites', {})
    print(f"✅ Registry loaded: {len(sites)} sites")
    print(f"   Enabled sites: {sum(1 for s in sites.values() if s.get('enabled', True))}")
    print(f"   Sites: {', '.join(sites.keys())}")
else:
    print(f"❌ Registry not found: {registry_path}")
