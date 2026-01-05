#!/usr/bin/env python3
"""
Website Audit Tool
==================

Audits all websites in the portfolio by checking their HTTP status,
WordPress health, and overall availability.

Features:
- Check HTTP status for all websites
- Test WordPress sites specifically
- Report detailed status for each site
- Identify down/unreachable sites
- Provide summary statistics

Usage:
    python audit_websites.py          # Audit all websites
    python audit_websites.py --site <domain>  # Audit specific site
    python audit_websites.py --wordpress     # Check WordPress sites only
    python audit_websites.py --summary       # Show only summary

Author: The Swarm (Multi-Agent AI System)
Date: 2026-01-01
"""

import argparse
import json
import sys
import requests
import time
from pathlib import Path
from typing import Dict, List, Optional, Tuple
from urllib.parse import urljoin
import yaml

# Disable SSL warnings for self-signed certificates
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


class WebsiteAuditor:
    """Website auditing system."""

    def __init__(self):
        self.project_root = Path(__file__).parent
        self.sites_config_path = self.project_root / "sites"
        self.websites_path = self.project_root / "websites"
        self.config_path = self.project_root / "config"

        # Load site configurations
        self.site_mappings = self._load_site_mappings()
        self.unique_sites = self._get_unique_sites()

    def _load_site_mappings(self) -> Dict[str, str]:
        """Load site ID to domain mappings from YAML files."""
        mappings = {}

        if self.sites_config_path.exists():
            for item in self.sites_config_path.iterdir():
                if item.suffix == '.yaml' and item.name != 'README.md':
                    try:
                        with open(item, 'r') as f:
                            data = yaml.safe_load(f)
                            if data and 'site_id' in data and 'domain' in data:
                                mappings[data['site_id']] = data['domain']
                    except Exception as e:
                        print(f"⚠️  Could not load {item}: {e}")

        return mappings

    def _get_unique_sites(self) -> List[str]:
        """Get list of unique website domains."""
        sites = set()

        # From websites directory
        if self.websites_path.exists():
            for item in self.websites_path.iterdir():
                if item.is_dir() and not item.name.startswith('.'):
                    sites.add(item.name)

        # From site mappings (ensure we have full domains)
        for domain in self.site_mappings.values():
            sites.add(domain)

        # Manual additions for known sites not in directories
        known_sites = [
            'weareswarm.site',
            'digitaldreamscape.site',
            'ariajet.site',
            'prismblossom.online'
        ]
        for site in known_sites:
            sites.add(site)

        return sorted(list(sites))

    def audit_all_sites(self, wordpress_only: bool = False) -> Dict[str, Dict]:
        """Audit all websites."""
        sites = self.unique_sites
        if wordpress_only:
            # Filter for WordPress sites
            wordpress_sites = []
            for site in sites:
                if self._is_wordpress_site(site):
                    wordpress_sites.append(site)
            sites = wordpress_sites

        print(f"\n🔍 AUDITING {len(sites)} WEBSITES")
        print("=" * 60)

        results = {}
        for site in sites:
            print(f"\n🌐 Checking {site}...")
            status = self.audit_site(site)
            results[site] = status

            # Print immediate status
            if status['http_status'] == 200:
                print("   ✅ Online")
            elif status['http_status'] > 0:
                print(f"   ⚠️  HTTP {status['http_status']}")
            else:
                print("   ❌ Offline/Down")
        return results

    def audit_site(self, domain: str) -> Dict:
        """Audit a single website."""
        result = {
            'domain': domain,
            'url': f'https://{domain}',
            'http_status': 0,
            'response_time': None,
            'wordpress': False,
            'wp_version': None,
            'error': None,
            'redirect_url': None
        }

        try:
            # Make HTTP request
            start_time = time.time()
            response = requests.get(
                result['url'],
                timeout=10,
                headers={'User-Agent': 'WebsiteAuditor/1.0'},
                verify=False,
                allow_redirects=True
            )
            end_time = time.time()

            result['http_status'] = response.status_code
            result['response_time'] = round((end_time - start_time) * 1000, 2)  # ms

            if response.history:
                result['redirect_url'] = response.url

            # Check if WordPress
            if self._is_wordpress_response(response):
                result['wordpress'] = True
                result['wp_version'] = self._get_wordpress_version(response)

        except requests.exceptions.Timeout:
            result['error'] = 'Timeout'
        except requests.exceptions.ConnectionError:
            result['error'] = 'Connection failed'
        except requests.exceptions.SSLError:
            result['error'] = 'SSL certificate error'
        except Exception as e:
            result['error'] = str(e)

        return result

    def _is_wordpress_site(self, domain: str) -> bool:
        """Check if a domain is a WordPress site based on configuration."""
        # Check if domain has WordPress config
        for site_id, site_domain in self.site_mappings.items():
            if site_domain == domain:
                # Look for WordPress publish config
                config_file = self.sites_config_path / f"{site_id}.yaml"
                if config_file.exists():
                    try:
                        with open(config_file, 'r') as f:
                            data = yaml.safe_load(f)
                            return data.get('publish', {}).get('provider') == 'wordpress'
                    except:
                        pass

        # Check if website has WordPress files
        site_path = self.websites_path / domain
        if site_path.exists():
            # Look for wp-content, wp-includes, etc.
            wp_indicators = ['wp-content', 'wp-includes', 'wp-admin']
            for indicator in wp_indicators:
                if (site_path / indicator).exists():
                    return True

        return False

    def _is_wordpress_response(self, response) -> bool:
        """Check if HTTP response indicates WordPress."""
        # Check for WordPress-specific headers or content
        if 'wordpress' in response.headers.get('server', '').lower():
            return True

        # Check for WordPress meta tags
        if 'wp-content' in response.text or 'wp-includes' in response.text:
            return True

        # Check for WordPress generator meta tag
        if 'name="generator" content="WordPress' in response.text:
            return True

        return False

    def _get_wordpress_version(self, response) -> Optional[str]:
        """Extract WordPress version from response."""
        import re

        # Look for generator meta tag
        match = re.search(r'name="generator" content="WordPress ([^"]*)"', response.text)
        if match:
            return match.group(1)

        # Look for version in wp-includes URLs
        match = re.search(r'wp-includes/js/wp-embed\.min\.js\?ver=([0-9]+\.[0-9]+\.[0-9]+)', response.text)
        if match:
            return match.group(1)

        return None

    def print_summary(self, results: Dict[str, Dict]) -> None:
        """Print audit summary."""
        print("\n" + "=" * 60)
        print("📊 AUDIT SUMMARY")
        print("=" * 60)

        total_sites = len(results)
        online_sites = sum(1 for r in results.values() if r['http_status'] == 200)
        offline_sites = total_sites - online_sites
        wordpress_sites = sum(1 for r in results.values() if r['wordpress'])

        print(f"Total websites: {total_sites}")
        print(f"✅ Online: {online_sites}")
        print(f"❌ Offline/Down: {offline_sites}")
        print(f"🔧 WordPress sites: {wordpress_sites}")

        if offline_sites > 0:
            print(f"\n❌ DOWN/OFFLINE SITES:")
            for domain, result in results.items():
                if result['http_status'] != 200:
                    error_msg = f" ({result['error']})" if result['error'] else ""
                    print(f"   - {domain}: HTTP {result['http_status']}{error_msg}")

        print("\n✅ ONLINE SITES:")
        for domain, result in results.items():
            if result['http_status'] == 200:
                wp_info = f" (WordPress {result['wp_version']})" if result['wordpress'] and result['wp_version'] else " (WordPress)" if result['wordpress'] else ""
                response_time = f" - {result['response_time']}ms" if result['response_time'] else ""
                print(f"   - {domain}{wp_info}{response_time}")


def main():
    """Main entry point."""
    parser = argparse.ArgumentParser(
        description='Website Audit Tool',
        formatter_class=argparse.RawDescriptionHelpFormatter,
    )

    parser.add_argument('--site', type=str, help='Audit specific site')
    parser.add_argument('--wordpress', action='store_true', help='Check WordPress sites only')
    parser.add_argument('--summary', action='store_true', help='Show only summary')

    args = parser.parse_args()

    auditor = WebsiteAuditor()

    try:
        if args.site:
            print(f"\n🔍 AUDITING SINGLE SITE: {args.site}")
            print("=" * 60)
            result = auditor.audit_site(args.site)

            if result['http_status'] == 200:
                print("   ✅ Online")
            elif result['http_status'] > 0:
                print(f"   ⚠️  HTTP {result['http_status']}")
            else:
                print("   ❌ Offline/Down")
            if result['wordpress']:
                wp_ver = f" {result['wp_version']}" if result['wp_version'] else ""
                print(f"   🔧 WordPress{wp_ver}")

            if result['response_time']:
                print(f"   ⏱️  Response time: {result['response_time']}ms")

            if result['error']:
                print(f"   ❌ Error: {result['error']}")

        else:
            results = auditor.audit_all_sites(wordpress_only=args.wordpress)

            if not args.summary:
                auditor.print_summary(results)
            else:
                # Just show counts
                total = len(results)
                online = sum(1 for r in results.values() if r['http_status'] == 200)
                offline = total - online
                wordpress = sum(1 for r in results.values() if r['wordpress'])

                print(f"\n📊 SUMMARY: {online}/{total} sites online, {wordpress} WordPress sites")

    except KeyboardInterrupt:
        print("\n⚠️  Audit cancelled by user")
        return 1
    except Exception as e:
        print(f"\n❌ Unexpected error: {e}")
        import traceback
        traceback.print_exc()
        return 1


if __name__ == '__main__':
    exit(main())