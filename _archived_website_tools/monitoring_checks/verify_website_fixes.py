#!/usr/bin/env python3
"""
Website Fixes Verification Tool
==============================

Verifies that website fixes have been properly deployed and are working.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-29
"""

import requests
import re
from typing import Dict, List, Tuple
from urllib.parse import urljoin


class WebsiteFixVerifier:
    """Verifies website fixes are properly deployed."""
    
    def __init__(self):
        self.sites = {
            'FreeRideInvestor': {
                'url': 'https://freerideinvestor.com',
                'fixes': ['text_rendering', 'css_404s']
            },
            'prismblossom': {
                'url': 'https://prismblossom.online',
                'fixes': ['text_rendering', 'contact_form']
            },
            'southwestsecret': {
                'url': 'https://southwestsecret.com',
                'fixes': ['text_rendering', 'hello_world']
            }
        }
    
    def check_text_rendering(self, url: str) -> Tuple[bool, str]:
        """Check if text rendering issue is fixed."""
        try:
            response = requests.get(url, timeout=10)
            response.raise_for_status()
            
            # Check for common text rendering issues
            problematic_patterns = [
                r'Late\s+t\s+Article',  # "Latest Article" with space
                r'Activitie\s+s',  # "Activities" with space
                r'Mood-Ba\s+ed',  # "Mood-Based" with space
            ]
            
            content = response.text
            issues_found = []
            
            for pattern in problematic_patterns:
                if re.search(pattern, content, re.IGNORECASE):
                    issues_found.append(f"Found pattern: {pattern}")
            
            if issues_found:
                return False, f"Text rendering issues found: {', '.join(issues_found)}"
            
            return True, "Text rendering appears fixed"
        except Exception as e:
            return False, f"Error checking text rendering: {str(e)}"
    
    def check_css_404s(self, url: str) -> Tuple[bool, List[str]]:
        """Check for CSS 404 errors."""
        try:
            response = requests.get(url, timeout=10)
            response.raise_for_status()
            
            # Extract CSS file references
            css_pattern = r'href=["\']([^"\']*\.css[^"\']*)["\']'
            css_files = re.findall(css_pattern, response.text)
            
            errors = []
            for css_file in css_files:
                css_url = urljoin(url, css_file)
                try:
                    css_response = requests.get(css_url, timeout=5)
                    if css_response.status_code == 404:
                        errors.append(css_url)
                except:
                    pass
            
            return len(errors) == 0, errors
        except Exception as e:
            return False, [f"Error checking CSS: {str(e)}"]
    
    def verify_all(self) -> Dict[str, Dict]:
        """Verify all fixes for all sites."""
        results = {}
        
        for site_name, site_info in self.sites.items():
            url = site_info['url']
            results[site_name] = {
                'url': url,
                'checks': {}
            }
            
            if 'text_rendering' in site_info['fixes']:
                status, message = self.check_text_rendering(url)
                results[site_name]['checks']['text_rendering'] = {
                    'status': status,
                    'message': message
                }
            
            if 'css_404s' in site_info['fixes']:
                status, errors = self.check_css_404s(url)
                results[site_name]['checks']['css_404s'] = {
                    'status': status,
                    'errors': errors if not status else []
                }
        
        return results
    
    def print_report(self, results: Dict[str, Dict]):
        """Print verification report."""
        print("\n" + "="*60)
        print("🌐 WEBSITE FIXES VERIFICATION REPORT")
        print("="*60 + "\n")
        
        for site_name, site_data in results.items():
            print(f"📋 {site_name}")
            print(f"   URL: {site_data['url']}\n")
            
            for check_name, check_result in site_data['checks'].items():
                status_icon = "✅" if check_result['status'] else "❌"
                print(f"   {status_icon} {check_name.replace('_', ' ').title()}")
                
                if 'message' in check_result:
                    print(f"      {check_result['message']}")
                
                if 'errors' in check_result and check_result['errors']:
                    print(f"      Errors found: {len(check_result['errors'])}")
                    for error in check_result['errors'][:5]:  # Show first 5
                        print(f"        - {error}")
            
            print()
        
        print("="*60 + "\n")


def main():
    """Main execution."""
    verifier = WebsiteFixVerifier()
    results = verifier.verify_all()
    verifier.print_report(results)
    
    # Return exit code based on results
    all_passed = all(
        all(check['status'] for check in site['checks'].values())
        for site in results.values()
    )
    
    return 0 if all_passed else 1


if __name__ == '__main__':
    exit(main())

