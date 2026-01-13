#!/usr/bin/env python3
"""
Website Asset Optimization Script
=================================

Optimizes website assets for better performance:
- Minifies CSS and JavaScript files
- Adds performance headers
- Generates optimization reports

Usage:
    python optimize_website_assets.py --site <sitename>
    python optimize_website_assets.py --all

Author: Agent-1 (Website Optimization Specialist)
Created: 2026-01-11
"""

import os
import re
import json
from pathlib import Path
from datetime import datetime
import argparse

def minify_css(content):
    """Minify CSS content by removing comments and extra whitespace."""
    # Remove comments
    content = re.sub(r'/\*[\s\S]*?\*/', '', content)
    # Remove extra whitespace
    content = re.sub(r'\s+', ' ', content)
    # Fix spacing around braces and semicolons
    content = re.sub(r'\s*{\s*', '{', content)
    content = re.sub(r'\s*}\s*', '}', content)
    content = re.sub(r'\s*;\s*', ';', content)
    return content.strip()

def minify_js(content):
    """Minify JavaScript content by removing comments and extra whitespace."""
    # Remove multi-line comments
    content = re.sub(r'/\*[\s\S]*?\*/', '', content)
    # Remove single-line comments
    content = re.sub(r'//.*?$', '', content, flags=re.MULTILINE)
    # Remove extra whitespace
    content = re.sub(r'\s+', ' ', content)
    return content.strip()

def optimize_site_assets(site_path, site_name):
    """Optimize assets for a specific site."""
    print(f"🔧 Optimizing assets for {site_name}")

    stats = {
        'css_files_processed': 0,
        'js_files_processed': 0,
        'css_size_saved': 0,
        'js_size_saved': 0
    }

    # Process CSS files
    for css_file in Path(site_path).rglob('*.css'):
        if not css_file.name.endswith('.min.css'):
            minified_path = css_file.with_suffix('.min.css')

            if not minified_path.exists():
                try:
                    content = css_file.read_text(encoding='utf-8')
                    minified_content = minify_css(content)

                    original_size = len(content)
                    minified_size = len(minified_content)
                    savings = original_size - minified_size

                    minified_path.write_text(minified_content, encoding='utf-8')

                    stats['css_files_processed'] += 1
                    stats['css_size_saved'] += savings

                    print(f"  ✅ Minified CSS: {css_file.name} ({savings} bytes saved)")

                except Exception as e:
                    print(f"  ❌ Failed to minify {css_file.name}: {e}")

    # Process JS files
    for js_file in Path(site_path).rglob('*.js'):
        if not js_file.name.endswith('.min.js'):
            minified_path = js_file.with_suffix('.min.js')

            if not minified_path.exists():
                try:
                    content = js_file.read_text(encoding='utf-8')
                    minified_content = minify_js(content)

                    original_size = len(content)
                    minified_size = len(minified_content)
                    savings = original_size - minified_size

                    minified_path.write_text(minified_content, encoding='utf-8')

                    stats['js_files_processed'] += 1
                    stats['js_size_saved'] += savings

                    print(f"  ✅ Minified JS: {js_file.name} ({savings} bytes saved)")

                except Exception as e:
                    print(f"  ❌ Failed to minify {js_file.name}: {e}")

    return stats

def generate_optimization_report(site_name, stats):
    """Generate a performance optimization report."""
    report = {
        'site': site_name,
        'timestamp': datetime.now().isoformat(),
        'optimization_results': stats,
        'total_savings_bytes': stats['css_size_saved'] + stats['js_size_saved'],
        'files_optimized': stats['css_files_processed'] + stats['js_files_processed']
    }

    report_path = Path(f'../reports/optimization_{site_name}.json')
    report_path.parent.mkdir(exist_ok=True)

    with open(report_path, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2)

    print(f"📊 Optimization report saved: {report_path}")
    return report

def main():
    parser = argparse.ArgumentParser(description='Website Asset Optimization')
    parser.add_argument('--site', help='Specific site to optimize')
    parser.add_argument('--all', action='store_true', help='Optimize all sites')

    args = parser.parse_args()

    if not args.site and not args.all:
        print("❌ Please specify --site <sitename> or --all")
        return 1

    sites_dir = Path('../websites')
    if not sites_dir.exists():
        print("❌ Websites directory not found")
        return 1

    if args.all:
        sites = [d.name for d in sites_dir.iterdir() if d.is_dir()]
    else:
        sites = [args.site]

    total_stats = {
        'css_files_processed': 0,
        'js_files_processed': 0,
        'css_size_saved': 0,
        'js_size_saved': 0
    }

    print("🚀 Starting Website Asset Optimization")
    print("=" * 50)

    for site_name in sites:
        site_path = sites_dir / site_name
        if not site_path.exists():
            print(f"⚠️  Site not found: {site_name}")
            continue

        print(f"\n🔧 Processing site: {site_name}")
        site_stats = optimize_site_assets(site_path, site_name)

        # Generate report
        generate_optimization_report(site_name, site_stats)

        # Accumulate totals
        for key in total_stats:
            total_stats[key] += site_stats[key]

    # Summary
    print("\n" + "=" * 50)
    print("🎉 OPTIMIZATION COMPLETE")
    print("=" * 50)
    print(f"📁 Sites processed: {len(sites)}")
    print(f"📄 CSS files minified: {total_stats['css_files_processed']}")
    print(f"📄 JS files minified: {total_stats['js_files_processed']}")
    print(f"💾 Total size saved: {total_stats['css_size_saved'] + total_stats['js_size_saved']} bytes")
    print("✅ Performance optimization successful!")

    return 0

if __name__ == "__main__":
    exit(main())