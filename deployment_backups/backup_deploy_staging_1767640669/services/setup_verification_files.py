#!/usr/bin/env python3
"""Setup verification files for all canonical domains."""

import os
from pathlib import Path

domains = {
    'dadudekc.com': {
        'markers': [
            'WEBSITE_DEPLOYED_DADUDEKC_COM_2026',
            'VICTOR_SYSTEMS_BUILDER_2026',
            'SWARM_POWERED_PORTFOLIO_2026',
            'DADUDEKC_THEME_ACTIVE_2026'
        ],
        'urls': [
            '/|200|WEBSITE_DEPLOYED_DADUDEKC_COM_2026',
            '/wp-admin/|200|Dashboard',
            '/contact/|200|contact',
            '/about/|200|Victor'
        ]
    },
    'freerideinvestor.com': {
        'markers': [
            'WEBSITE_DEPLOYED_FREERIDEINVESTOR_COM_2026',
            'TRADING_SYSTEMS_ACTIVE_2026',
            'FREERIDEINVESTOR_THEME_ACTIVE_2026'
        ],
        'urls': [
            '/|200|WEBSITE_DEPLOYED_FREERIDEINVESTOR_COM_2026',
            '/wp-admin/|200|Dashboard',
            '/trading/|200|trading'
        ]
    },
    'tradingrobotplug.com': {
        'markers': [
            'WEBSITE_DEPLOYED_TRADINGROBOTPLUG_COM_2026',
            'MARKETPLACE_ACTIVE_2026',
            'TRADINGROBOTPLUG_THEME_ACTIVE_2026'
        ],
        'urls': [
            '/|200|WEBSITE_DEPLOYED_TRADINGROBOTPLUG_COM_2026',
            '/wp-admin/|200|Dashboard',
            '/marketplace/|200|marketplace',
            '/dashboard/|200|dashboard'
        ]
    },
    'crosbyultimateevents.com': {
        'markers': [
            'WEBSITE_DEPLOYED_CROSBYULTIMATEEVENTS_COM_2026',
            'EVENT_MANAGEMENT_ACTIVE_2026',
            'CROSBYULTIMATEEVENTS_THEME_ACTIVE_2026'
        ],
        'urls': [
            '/|200|WEBSITE_DEPLOYED_CROSBYULTIMATEEVENTS_COM_2026',
            '/wp-admin/|200|Dashboard',
            '/events/|200|events',
            '/consultation/|200|consultation'
        ]
    }
}

for domain, config in domains.items():
    verify_dir = Path(f"websites/{domain}/ops/verify")
    verify_dir.mkdir(parents=True, exist_ok=True)

    # Create markers.txt
    with open(verify_dir / "markers.txt", 'w') as f:
        f.write(f"# {domain} Verification Markers\n")
        f.write("# These unique strings must appear on deployed pages\n\n")
        for marker in config['markers']:
            f.write(f"{marker}\n")

    # Create urls.txt
    with open(verify_dir / "urls.txt", 'w') as f:
        f.write(f"# {domain} URLs to verify after deployment\n")
        f.write("# Format: URL|expected_status|marker_to_check\n\n")
        for url in config['urls']:
            f.write(f"{url}\n")

    print(f"✅ Created verification files for {domain}")

print("\n🎉 All verification files created!")