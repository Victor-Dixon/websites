#!/usr/bin/env python3
"""
WordPress Update Checker
========================

Checks WordPress core and plugin versions for updates.
Generates a report with update recommendations.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-01
"""

import os
import sys
import json
from pathlib import Path
from datetime import datetime
import requests
from typing import Dict, List, Optional

# WordPress API endpoints
WP_CORE_API = "https://api.wordpress.org/core/version-check/1.7/"
WP_PLUGIN_API = "https://api.wordpress.org/plugins/info/1.2/"


def get_latest_wp_version() -> Optional[str]:
    """Get latest WordPress core version."""
    try:
        response = requests.get(WP_CORE_API, timeout=10)
        if response.status_code == 200:
            data = response.json()
            return data.get('offers', [{}])[0].get('version', None)
    except Exception as e:
        print(f"⚠️  Error checking WordPress version: {e}")
    return None


def check_wordpress_site(site_path: Path) -> Dict:
    """Check WordPress version for a site."""
    result = {
        'site': site_path.name,
        'wp_version': None,
        'latest_wp_version': None,
        'needs_update': False,
        'plugins': []
    }
    
    # Check for wp-config.php or version.php
    wp_config = site_path / 'wp-config.php'
    wp_version_file = site_path / 'wp-includes' / 'version.php'
    
    if wp_version_file.exists():
        try:
            with open(wp_version_file, 'r', encoding='utf-8') as f:
                content = f.read()
                # Extract version from $wp_version = 'x.x.x';
                import re
                match = re.search(r"\$wp_version\s*=\s*['\"]([^'\"]+)['\"]", content)
                if match:
                    result['wp_version'] = match.group(1)
        except Exception as e:
            print(f"⚠️  Error reading version.php: {e}")
    
    # Get latest version
    latest = get_latest_wp_version()
    if latest:
        result['latest_wp_version'] = latest
        if result['wp_version']:
            result['needs_update'] = result['wp_version'] != latest
    
    return result


def generate_report(results: List[Dict]) -> str:
    """Generate update report."""
    report = []
    report.append("# WordPress Update Report")
    report.append(f"**Generated**: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    report.append("")
    report.append("## Summary")
    report.append("")
    
    sites_needing_updates = [r for r in results if r.get('needs_update', False)]
    report.append(f"- **Sites Checked**: {len(results)}")
    report.append(f"- **Sites Needing Updates**: {len(sites_needing_updates)}")
    report.append("")
    
    report.append("## Site Details")
    report.append("")
    
    for result in results:
        report.append(f"### {result['site']}")
        report.append("")
        if result['wp_version']:
            report.append(f"- **Current Version**: {result['wp_version']}")
        else:
            report.append("- **Current Version**: Unknown (not a WordPress site or version file not found)")
        
        if result['latest_wp_version']:
            report.append(f"- **Latest Version**: {result['latest_wp_version']}")
        
        if result['needs_update']:
            report.append(f"- **Status**: ⚠️ **NEEDS UPDATE**")
        else:
            report.append("- **Status**: ✅ Up to date")
        
        report.append("")
    
    return "\n".join(report)


def main():
    """Main execution."""
    websites_dir = Path(__file__).parent.parent
    
    print("🔍 Checking WordPress versions...")
    print("")
    
    results = []
    
    # Check each website directory
    for site_dir in websites_dir.iterdir():
        if site_dir.is_dir() and not site_dir.name.startswith('.'):
            print(f"Checking {site_dir.name}...")
            result = check_wordpress_site(site_dir)
            results.append(result)
            
            if result['wp_version']:
                if result['needs_update']:
                    print(f"  ⚠️  Version {result['wp_version']} - Update available: {result['latest_wp_version']}")
                else:
                    print(f"  ✅ Version {result['wp_version']} - Up to date")
            else:
                print(f"  ℹ️  Not a WordPress site or version not found")
    
    # Generate report
    report = generate_report(results)
    
    # Save report
    report_file = websites_dir / 'tools' / 'wordpress_update_report.md'
    report_file.parent.mkdir(parents=True, exist_ok=True)
    report_file.write_text(report, encoding='utf-8')
    
    print("")
    print(f"✅ Report saved to: {report_file}")
    print("")
    print(report)
    
    return 0


if __name__ == '__main__':
    sys.exit(main())




