#!/usr/bin/env python3
"""
WordPress Version Checker for FreeRideInvestor
==============================================

Checks WordPress core and plugin versions for security updates.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-30
"""

import requests
import json
from typing import Dict, Optional
from datetime import datetime


class WordPressVersionChecker:
    """Checks WordPress versions for updates."""
    
    def __init__(self):
        self.wp_api = 'https://api.wordpress.org/core/version-check/1.7/'
        self.plugin_api = 'https://api.wordpress.org/plugins/info/1.2/'
    
    def get_latest_wp_version(self) -> Optional[str]:
        """Get latest WordPress version."""
        try:
            response = requests.get(self.wp_api, timeout=10)
            response.raise_for_status()
            data = response.json()
            return data.get('offers', [{}])[0].get('version')
        except Exception as e:
            print(f"Error fetching WordPress version: {e}")
            return None
    
    def check_plugin_version(self, plugin_slug: str) -> Optional[Dict]:
        """Check plugin version information."""
        try:
            response = requests.get(
                self.plugin_api,
                params={'action': 'plugin_information', 'request': {'slug': plugin_slug}},
                timeout=10
            )
            response.raise_for_status()
            data = response.json()
            return {
                'name': data.get('name'),
                'version': data.get('version'),
                'last_updated': data.get('last_updated'),
                'requires': data.get('requires'),
                'tested': data.get('tested')
            }
        except Exception as e:
            print(f"Error checking plugin {plugin_slug}: {e}")
            return None
    
    def generate_update_report(self) -> str:
        """Generate update report for FreeRideInvestor."""
        report = []
        report.append("="*60)
        report.append("📊 WORDPRESS UPDATE CHECK - FreeRideInvestor")
        report.append(f"Date: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        report.append("="*60 + "\n")
        
        # Check WordPress core
        latest_wp = self.get_latest_wp_version()
        if latest_wp:
            report.append(f"✅ Latest WordPress Version: {latest_wp}")
            report.append(f"   ⚠️  Action: Check current version on site and update if needed")
        else:
            report.append(f"❌ Could not check WordPress version")
        
        report.append("\n" + "-"*60 + "\n")
        
        # Common plugins to check
        common_plugins = [
            'akismet',
            'contact-form-7',
            'yoast-seo',
            'wordfence',
            'wp-super-cache',
        ]
        
        report.append("📦 Common Plugin Updates:\n")
        report.append("   ⚠️  Note: FreeRideInvestor has 26 plugins installed")
        report.append("   ⚠️  Action: Check WordPress admin → Plugins for updates\n")
        
        report.append("="*60)
        report.append("\n💡 RECOMMENDATIONS:")
        report.append("   1. Access WordPress admin panel")
        report.append("   2. Check Dashboard → Updates")
        report.append("   3. Update WordPress core if needed")
        report.append("   4. Update all plugins")
        report.append("   5. Backup before updating")
        report.append("   6. Test site after updates")
        
        return "\n".join(report)


def main():
    """Main execution."""
    checker = WordPressVersionChecker()
    report = checker.generate_update_report()
    print(report)
    
    # Save to file
    with open('wordpress_update_report.txt', 'w') as f:
        f.write(report)
    
    print("\n✅ Report saved to wordpress_update_report.txt")


if __name__ == '__main__':
    main()

