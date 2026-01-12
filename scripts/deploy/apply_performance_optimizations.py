#!/usr/bin/env python3
"""
Website Performance Optimization Deployment Script
================================================

Applies performance and security optimizations across all WordPress websites.

Optimizations Applied:
- Security headers removal
- Asset loading optimization
- Database query optimization
- Resource hints for external assets
- Emoji disabling for performance
- WooCommerce optimization (if active)

Author: Agent-3 (Infrastructure & Deployment Specialist)
Date: 2026-01-11
"""

import os
import re
from pathlib import Path
from typing import List, Dict

class WebsiteOptimizer:
    """Applies performance optimizations to WordPress websites."""

    def __init__(self, websites_root: Path):
        self.websites_root = websites_root
        self.optimized_sites = []

    def find_wordpress_sites(self) -> List[Path]:
        """Find all WordPress sites in the websites directory."""
        sites = []
        for item in self.websites_root.iterdir():
            if item.is_dir():
                wp_path = item / "wp" / "wp-content" / "themes"
                if wp_path.exists():
                    sites.append(item)
        return sites

    def optimize_functions_php(self, functions_path: Path) -> bool:
        """Apply performance optimizations to functions.php."""
        try:
            with open(functions_path, 'r', encoding='utf-8') as f:
                content = f.read()

            # Check if optimizations are already applied
            if 'freerideinvestor_security_headers' in content:
                print(f"  ⚠️  Optimizations already applied to {functions_path}")
                return False

            # Find the closing PHP tag and add optimizations before it
            if content.strip().endswith('?>'):
                # Remove the closing tag temporarily
                content = content.rstrip()
                if content.endswith('?>'):
                    content = content[:-2].rstrip()

                # Add optimizations
                optimizations = self.get_optimization_code()
                content += '\n\n' + optimizations + '\n?>'

                # Write back
                with open(functions_path, 'w', encoding='utf-8') as f:
                    f.write(content)

                print(f"  ✅ Applied optimizations to {functions_path}")
                return True
            else:
                print(f"  ❌ Could not find closing PHP tag in {functions_path}")
                return False

        except Exception as e:
            print(f"  ❌ Error optimizing {functions_path}: {e}")
            return False

    def get_optimization_code(self) -> str:
        """Get the optimization code to add to functions.php."""
        return '''/**
 * Performance and Security Optimizations
 */

/**
 * Add security headers
 */
function freerideinvestor_security_headers() {
    if (!is_admin()) {
        // Remove WordPress version from head
        remove_action('wp_head', 'wp_generator');

        // Remove RSD link
        remove_action('wp_head', 'rsd_link');

        // Remove Windows Live Writer
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');
    }
}
add_action('init', 'freerideinvestor_security_headers');

/**
 * Optimize asset loading - defer non-critical CSS
 */
function freerideinvestor_optimize_assets() {
    // Defer non-critical styles
    if (!is_admin()) {
        add_filter('style_loader_tag', 'freerideinvestor_defer_styles', 10, 4);
    }
}
add_action('wp_enqueue_scripts', 'freerideinvestor_optimize_assets', 999);

/**
 * Defer loading of non-critical styles
 */
function freerideinvestor_defer_styles($html, $handle, $href, $media) {
    // Defer Tailwind CSS as it's not critical for initial render
    if ($handle === 'tailwind-css') {
        return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
    }
    return $html;
}

/**
 * Optimize database queries
 */
function freerideinvestor_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_home()) {
        // Limit posts per page for performance
        $query->set('posts_per_page', 12);
    }
    return $query;
}
add_action('pre_get_posts', 'freerideinvestor_optimize_queries');

/**
 * Add preconnect for external resources
 */
function freerideinvestor_resource_hints($hints, $relation_type) {
    if ($relation_type === 'preconnect') {
        // Preconnect to Google Fonts
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
    }
    return $hints;
}
add_filter('wp_resource_hints', 'freerideinvestor_resource_hints', 10, 2);

/**
 * Disable emojis for performance
 */
function freerideinvestor_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'freerideinvestor_disable_emojis');'''

    def optimize_site(self, site_path: Path) -> bool:
        """Optimize a single WordPress site."""
        print(f"\n🔧 Optimizing site: {site_path.name}")

        # Find all theme functions.php files
        themes_dir = site_path / "wp" / "wp-content" / "themes"
        if not themes_dir.exists():
            print(f"  ❌ No themes directory found for {site_path.name}")
            return False

        optimized = False
        for theme_dir in themes_dir.iterdir():
            if theme_dir.is_dir():
                functions_path = theme_dir / "functions.php"
                if functions_path.exists():
                    if self.optimize_functions_php(functions_path):
                        optimized = True

        if optimized:
            self.optimized_sites.append(site_path.name)

        return optimized

    def deploy_optimizations(self) -> Dict[str, int]:
        """Deploy optimizations across all websites."""
        print("🚀 Starting website performance optimization deployment...")
        print("=" * 60)

        sites = self.find_wordpress_sites()
        print(f"📊 Found {len(sites)} WordPress sites to optimize")

        results = {
            'total_sites': len(sites),
            'optimized_sites': 0,
            'failed_sites': 0
        }

        for site in sites:
            try:
                if self.optimize_site(site):
                    results['optimized_sites'] += 1
                else:
                    results['failed_sites'] += 1
            except Exception as e:
                print(f"❌ Error processing site {site.name}: {e}")
                results['failed_sites'] += 1

        print("\n" + "=" * 60)
        print("📊 OPTIMIZATION DEPLOYMENT COMPLETE")
        print(f"✅ Successfully optimized: {results['optimized_sites']} sites")
        print(f"❌ Failed to optimize: {results['failed_sites']} sites")
        print(f"📋 Optimized sites: {', '.join(self.optimized_sites)}")

        return results

def main():
    """Main deployment function."""
    websites_root = Path("D:/websites/websites")

    if not websites_root.exists():
        print(f"❌ Websites directory not found: {websites_root}")
        return 1

    optimizer = WebsiteOptimizer(websites_root)
    results = optimizer.deploy_optimizations()

    return 0 if results['optimized_sites'] > 0 else 1

if __name__ == "__main__":
    exit(main())