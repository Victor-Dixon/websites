#!/usr/bin/env python3
"""
Diagnose FreeRideInvestor content visibility issues.
Check for CSS opacity: 0 and other content hiding issues.
"""

import requests
from bs4 import BeautifulSoup
import re
import json
from datetime import datetime

class FreeRideInvestorContentDiagnostic:
    def __init__(self, site_url="https://freerideinvestor.com"):
        self.site_url = site_url.rstrip('/')
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })

    def check_site_accessibility(self):
        """Check if site is accessible"""
        try:
            response = self.session.get(self.site_url, timeout=10)
            return {
                'status_code': response.status_code,
                'accessible': response.status_code == 200,
                'response_size': len(response.content)
            }
        except Exception as e:
            return {
                'status_code': None,
                'accessible': False,
                'error': str(e)
            }

    def analyze_html_content(self, html_content):
        """Analyze HTML for content visibility issues"""
        soup = BeautifulSoup(html_content, 'html.parser')

        issues = []

        # Check for main content areas
        main_content = soup.find('main') or soup.find(id='primary') or soup.find(class_='site-main')
        if main_content:
            # Check if main content has opacity: 0 in style attribute
            style_attr = main_content.get('style', '')
            if 'opacity: 0' in style_attr.lower():
                issues.append({
                    'type': 'opacity_zero',
                    'element': 'main',
                    'severity': 'CRITICAL',
                    'description': 'Main content area has opacity: 0'
                })

        # Check for CSS that might hide content
        css_rules = self.extract_inline_css(html_content)

        # Look for opacity: 0 rules
        opacity_zero_rules = []
        for rule in css_rules:
            if 'opacity: 0' in rule.lower() or 'opacity:0' in rule.lower():
                opacity_zero_rules.append(rule)

        if opacity_zero_rules:
            issues.append({
                'type': 'css_opacity_zero',
                'rules': opacity_zero_rules,
                'severity': 'CRITICAL',
                'description': f'Found {len(opacity_zero_rules)} CSS rules with opacity: 0'
            })

        # Check for display: none rules
        display_none_rules = []
        for rule in css_rules:
            if 'display: none' in rule.lower() or 'display:none' in rule.lower():
                display_none_rules.append(rule)

        if display_none_rules:
            issues.append({
                'type': 'css_display_none',
                'rules': display_none_rules,
                'severity': 'HIGH',
                'description': f'Found {len(display_none_rules)} CSS rules with display: none'
            })

        # Check for visibility: hidden rules
        visibility_hidden_rules = []
        for rule in css_rules:
            if 'visibility: hidden' in rule.lower() or 'visibility:hidden' in rule.lower():
                visibility_hidden_rules.append(rule)

        if visibility_hidden_rules:
            issues.append({
                'type': 'css_visibility_hidden',
                'rules': visibility_hidden_rules,
                'severity': 'HIGH',
                'description': f'Found {len(visibility_hidden_rules)} CSS rules with visibility: hidden'
            })

        # Check for empty content areas
        content_check = self.check_content_presence(soup)
        issues.extend(content_check)

        return issues

    def extract_inline_css(self, html_content):
        """Extract inline CSS rules from HTML"""
        css_rules = []

        # Find style tags
        style_pattern = r'<style[^>]*>(.*?)</style>'
        style_matches = re.findall(style_pattern, html_content, re.DOTALL | re.IGNORECASE)

        for style_content in style_matches:
            # Split by braces and extract rules
            rules = re.findall(r'\{([^}]+)\}', style_content)
            css_rules.extend(rules)

        # Find style attributes
        style_attr_pattern = r'style=["\']([^"\']+)["\']'
        style_attrs = re.findall(style_attr_pattern, html_content, re.IGNORECASE)

        for attr in style_attrs:
            css_rules.append(attr)

        return css_rules

    def check_content_presence(self, soup):
        """Check if content areas have actual content"""
        issues = []

        # Check main content
        main_content = soup.find('main') or soup.find(id='primary') or soup.find(class_='site-main')
        if main_content:
            text_content = main_content.get_text(strip=True)
            if len(text_content) < 50:  # Very little content
                issues.append({
                    'type': 'insufficient_content',
                    'element': 'main',
                    'severity': 'CRITICAL',
                    'description': f'Main content area has very little text content ({len(text_content)} characters)'
                })

        # Check for hero section
        hero = soup.find(class_='hero-section') or soup.find(id='hero')
        if hero:
            hero_text = hero.get_text(strip=True)
            if len(hero_text) < 10:
                issues.append({
                    'type': 'empty_hero',
                    'element': 'hero',
                    'severity': 'HIGH',
                    'description': 'Hero section appears to be empty or has minimal content'
                })

        # Check for posts/articles
        posts = soup.find_all(class_='post') or soup.find_all('article')
        if len(posts) == 0:
            issues.append({
                'type': 'no_posts_found',
                'severity': 'MEDIUM',
                'description': 'No post/article elements found on homepage'
            })

        return issues

    def run_diagnosis(self):
        """Run complete diagnosis"""
        print(f"🔍 Diagnosing {self.site_url} content visibility issues...")
        print("=" * 60)

        # Check accessibility
        accessibility = self.check_site_accessibility()
        print(f"Site Accessibility: {'✅' if accessibility['accessible'] else '❌'}")
        print(f"Status Code: {accessibility.get('status_code', 'N/A')}")
        print(f"Response Size: {accessibility.get('response_size', 0)} bytes")

        if not accessibility['accessible']:
            return {
                'timestamp': datetime.now().isoformat(),
                'site_url': self.site_url,
                'accessibility': accessibility,
                'issues': [{'type': 'site_inaccessible', 'severity': 'CRITICAL', 'description': 'Site is not accessible'}]
            }

        # Get page content
        response = self.session.get(self.site_url)
        html_content = response.text

        # Analyze content
        issues = self.analyze_html_content(html_content)

        # Print results
        print(f"\nIssues Found: {len(issues)}")
        print("-" * 30)

        severity_counts = {'CRITICAL': 0, 'HIGH': 0, 'MEDIUM': 0, 'LOW': 0}
        for issue in issues:
            severity = issue.get('severity', 'UNKNOWN')
            severity_counts[severity] = severity_counts.get(severity, 0) + 1

            print(f"{severity}: {issue['description']}")
            if 'rules' in issue:
                for rule in issue['rules'][:3]:  # Show first 3 rules
                    print(f"  CSS Rule: {rule[:100]}...")

        print(f"\nSeverity Breakdown:")
        for severity, count in severity_counts.items():
            if count > 0:
                print(f"  {severity}: {count}")

        return {
            'timestamp': datetime.now().isoformat(),
            'site_url': self.site_url,
            'accessibility': accessibility,
            'issues': issues,
            'severity_counts': severity_counts
        }

def main():
    diagnostic = FreeRideInvestorContentDiagnostic()
    results = diagnostic.run_diagnosis()

    # Save results
    output_file = f"diagnostics/freerideinvestor_content_diagnosis_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"

    # Ensure diagnostics directory exists
    import os
    os.makedirs('diagnostics', exist_ok=True)

    with open(output_file, 'w') as f:
        json.dump(results, f, indent=2)

    print(f"\n📄 Results saved to: {output_file}")

    # Return critical status
    critical_issues = [i for i in results['issues'] if i.get('severity') == 'CRITICAL']
    if critical_issues:
        print("❌ CRITICAL ISSUES FOUND - Content visibility compromised")
        return 1
    else:
        print("✅ No critical content visibility issues detected")
        return 0

if __name__ == '__main__':
    exit(main())