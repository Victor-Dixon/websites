#!/usr/bin/env python3
"""
WordPress Version Checker
=========================

Checks WordPress core and plugin versions for security updates.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-29
"""

import requests
import json
from typing import Dict, List, Optional
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
    
    def generate_update_report(self, current_wp: str, plugins: List[str]) -> str:
        """Generate update report."""
        report = []
        report.append("="*60)
        report.append("📊 WORDPRESS UPDATE CHECK REPORT")
        report.append(f"Date: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        report.append("="*60 + "\n")
        
        # Check WordPress core
        latest_wp = self.get_latest_wp_version()
        if latest_wp:
            if current_wp != latest_wp:
                report.append(f"⚠️  WordPress Core Update Available")
                report.append(f"   Current: {current_wp}")
                report.append(f"   Latest: {latest_wp}")
                report.append(f"   Action: Update recommended")
            else:
                report.append(f"✅ WordPress Core Up to Date")
                report.append(f"   Version: {current_wp}")
        else:
            report.append(f"❌ Could not check WordPress version")
        
        report.append("\n" + "-"*60 + "\n")
        
        # Check plugins
        report.append("📦 Plugin Updates:\n")
        for plugin_slug in plugins:
            plugin_info = self.check_plugin_version(plugin_slug)
            if plugin_info:
                report.append(f"   {plugin_info['name']}")
                report.append(f"      Version: {plugin_info['version']}")
                report.append(f"      Last Updated: {plugin_info['last_updated']}")
                report.append(f"      Requires WP: {plugin_info['requires']}")
                report.append(f"      Tested with: {plugin_info['tested']}")
                report.append("")
        
        report.append("="*60)
        return "\n".join(report)


def main():
    """Main execution."""
    checker = WordPressVersionChecker()
    
    # Example usage - replace with actual versions
    current_wp = "6.4"  # Replace with actual version
    plugins = [
        'akismet',
        'contact-form-7',
        'yoast-seo',
        # Add more plugin slugs as needed
    ]
    
    report = checker.generate_update_report(current_wp, plugins)
    print(report)
    
    # Save to file
    with open('wordpress_update_report.txt', 'w') as f:
        f.write(report)
    
    print("\n✅ Report saved to wordpress_update_report.txt")


if __name__ == '__main__':
    main()

