#!/usr/bin/env python3
"""
Diagnose text rendering issues on websites.
Check for character spacing, encoding issues, and malformed text.
"""

import requests
from bs4 import BeautifulSoup
import re
import json
from datetime import datetime

class TextRenderingDiagnostic:
    def __init__(self, site_url):
        self.site_url = site_url.rstrip('/')
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })

    def get_page_content(self):
        """Get the page content"""
        try:
            response = self.session.get(self.site_url, timeout=10)
            if response.status_code == 200:
                return response.text
            else:
                return None
        except Exception as e:
            print(f"Error fetching {self.site_url}: {e}")
            return None

    def analyze_text_rendering(self, html_content):
        """Analyze text for rendering issues"""
        issues = []

        if not html_content:
            return issues

        soup = BeautifulSoup(html_content, 'html.parser')

        # Remove script and style elements
        for script in soup(["script", "style"]):
            script.extract()

        # Get all text content
        text_content = soup.get_text()

        # Check for known problematic patterns
        problematic_patterns = [
            (r'\bCapabilitie\b', 'Capabilities'),  # Missing 's'
            (r'\bWordPre\b', 'WordPress'),  # Missing 's'
            (r'\bre\s*erved\b', 'reserved'),  # Spaced 'reserved'
            (r'\bA multi-agent AI\s+y\s+tem\s+howca\s+ing\b', 'A multi-agent AI system showcasing'),  # Spaced text
            (r'\bSpecialize\s+d\s+in\s+y\s+tem\s+integration\b', 'Specialized in system integration'),  # Spaced text
            (r'\b©\s+2025\s+weare\s+warm\.online\b', '© 2025 weareswarm.online'),  # Spaced domain
        ]

        for pattern, correct_text in problematic_patterns:
            matches = re.findall(pattern, text_content, re.IGNORECASE)
            if matches:
                issues.append({
                    'type': 'malformed_text',
                    'pattern': pattern,
                    'found': matches,
                    'correct': correct_text,
                    'severity': 'HIGH',
                    'description': f'Found malformed text: "{matches[0]}" should be "{correct_text}"'
                })

        # Check for excessive spacing in words
        words_with_spacing = re.findall(r'\b\w+\s+\w+\s+\w+\b', text_content)
        for word in words_with_spacing[:10]:  # Check first 10
            if len(word.split()) > 2 and len(word) > 20:
                # Look for words that should be single words but are spaced
                if re.search(r'\b(system|integration|development|capabilities|specialized|showcasing)\b', word.lower()):
                    issues.append({
                        'type': 'word_spacing_issue',
                        'text': word,
                        'severity': 'MEDIUM',
                        'description': f'Potentially spaced word: "{word}"'
                    })

        # Check for HTML entity issues
        raw_html = str(soup)
        if '&#' in raw_html or '&nbsp' in raw_html:
            issues.append({
                'type': 'html_entities',
                'severity': 'LOW',
                'description': 'HTML entities found - check encoding'
            })

        return issues

    def check_content_sources(self, html_content):
        """Check where content is coming from"""
        sources = []

        if not html_content:
            return sources

        soup = BeautifulSoup(html_content, 'html.parser')

        # Check for WordPress indicators
        if soup.find('meta', attrs={'name': 'generator'}):
            generator = soup.find('meta', attrs={'name': 'generator'})
            if generator and 'WordPress' in generator.get('content', ''):
                sources.append('wordpress')

        # Check for specific content patterns
        text_content = soup.get_text()

        if 'multi-agent AI' in text_content.lower():
            sources.append('ai_content')
        if 'system architecture' in text_content.lower():
            sources.append('technical_content')
        if 'capabilities' in text_content.lower() or 'capabilitie' in text_content.lower():
            sources.append('services_content')

        return sources

    def run_diagnosis(self):
        """Run complete diagnosis"""
        print(f"🔍 Diagnosing text rendering issues on {self.site_url}")
        print("=" * 60)

        html_content = self.get_page_content()
        if not html_content:
            return {
                'timestamp': datetime.now().isoformat(),
                'site_url': self.site_url,
                'status': 'unreachable',
                'issues': []
            }

        issues = self.analyze_text_rendering(html_content)
        sources = self.check_content_sources(html_content)

        print(f"Content Sources: {', '.join(sources) if sources else 'Unknown'}")
        print(f"Issues Found: {len(issues)}")
        print("-" * 40)

        severity_counts = {'CRITICAL': 0, 'HIGH': 0, 'MEDIUM': 0, 'LOW': 0}
        for issue in issues:
            severity = issue.get('severity', 'UNKNOWN')
            severity_counts[severity] = severity_counts.get(severity, 0) + 1

            print(f"{severity}: {issue['description']}")
            if 'found' in issue:
                print(f"  Found: {issue['found']}")
            if 'correct' in issue:
                print(f"  Should be: {issue['correct']}")

        print(f"\nSeverity Breakdown:")
        for severity, count in severity_counts.items():
            if count > 0:
                print(f"  {severity}: {count}")

        return {
            'timestamp': datetime.now().isoformat(),
            'site_url': self.site_url,
            'status': 'analyzed',
            'content_sources': sources,
            'issues': issues,
            'severity_counts': severity_counts
        }

def main():
    # Focus on remaining sites that haven't been audited yet
    sites_to_check = [
        'https://ariajet.site',
        'https://dadudekc.com',
        'https://digitaldreamscape.site',
        'https://houstonsipqueen.com',
        'https://southwestsecret.com',
        'https://weareswarm.site'
    ]

    all_results = {}

    for site_url in sites_to_check:
        diagnostic = TextRenderingDiagnostic(site_url)
        results = diagnostic.run_diagnosis()
        all_results[site_url] = results

        # Save individual results
        output_file = f"diagnostics/text_rendering_{site_url.replace('https://', '').replace('.', '_')}_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        import os
        os.makedirs('diagnostics', exist_ok=True)

        with open(output_file, 'w') as f:
            json.dump(results, f, indent=2)

        print(f"📄 Results saved to: {output_file}\n")

    # Save summary
    summary_file = f"diagnostics/text_rendering_summary_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
    with open(summary_file, 'w') as f:
        json.dump(all_results, f, indent=2)

    print(f"📋 Summary saved to: {summary_file}")

if __name__ == '__main__':
    main()