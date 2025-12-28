#!/usr/bin/env python3
"""
Compare freerideinvestor-modern functions.php with Default Theme
================================================================

Compares the problematic theme's functions.php with a working default theme
to identify what might be preventing template execution.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def get_functions_php_content(deployer, theme_name):
    """Get functions.php content for a theme."""
    remote_path = "domains/freerideinvestor.com/public_html"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    exists = deployer.execute_command(f"test -f {functions_php} && echo 'EXISTS' || echo 'MISSING'")
    if "EXISTS" in exists:
        return deployer.execute_command(f"cat {functions_php}")
    return None


def analyze_functions_structure(content, theme_name):
    """Analyze functions.php structure."""
    if not content:
        return None
    
    analysis = {
        "theme": theme_name,
        "length": len(content),
        "has_setup": "after_setup_theme" in content,
        "has_wp_head": "wp_head" in content,
        "has_wp_footer": "wp_footer" in content,
        "has_template_include": "template_include" in content,
        "has_template_redirect": "template_redirect" in content,
        "has_output_buffering": "ob_start" in content or "ob_end" in content,
        "has_early_exits": "die()" in content or "exit()" in content or "wp_die" in content,
        "has_content_filters": "the_content" in content,
        "has_query_modifications": "pre_get_posts" in content,
    }
    
    return analysis


def check_for_problematic_patterns(content):
    """Check for patterns that might stop template execution."""
    problematic = []
    
    # Check for output buffering that might suppress content
    if "ob_start()" in content and "ob_get_clean()" in content:
        problematic.append("Output buffering found - might suppress template output")
    
    # Check for template_include filters that return early
    if "template_include" in content:
        # Look for patterns that return null/false
        if "return null" in content or "return false" in content or "return ''" in content:
            problematic.append("template_include filter might return null/false")
    
    # Check for template_redirect that might exit
    if "template_redirect" in content:
        if "exit" in content or "die" in content or "wp_redirect" in content:
            problematic.append("template_redirect hook might exit or redirect")
    
    # Check for wp action that might interfere
    if "add_action('wp'" in content:
        if "exit" in content or "die" in content or "wp_die" in content:
            problematic.append("wp action hook might exit")
    
    return problematic


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        print("=" * 70)
        print("COMPARING FUNCTIONS.PHP WITH DEFAULT THEME")
        print("=" * 70)
        
        # Get problematic theme's functions.php
        problematic_theme = "freerideinvestor-modern"
        problematic_content = get_functions_php_content(deployer, problematic_theme)
        
        # Get default theme's functions.php
        default_theme = "twentytwentyfour"
        default_content = get_functions_php_content(deployer, default_theme)
        
        if problematic_content:
            print(f"\n📄 {problematic_theme}/functions.php")
            analysis = analyze_functions_structure(problematic_content, problematic_theme)
            if analysis:
                print(f"   Length: {analysis['length']} bytes")
                print(f"   Has after_setup_theme: {analysis['has_setup']}")
                print(f"   Has template_include: {analysis['has_template_include']}")
                print(f"   Has template_redirect: {analysis['has_template_redirect']}")
                print(f"   Has output buffering: {analysis['has_output_buffering']}")
                print(f"   Has early exits: {analysis['has_early_exits']}")
                
                problematic = check_for_problematic_patterns(problematic_content)
                if problematic:
                    print(f"\n   ⚠️  Problematic patterns found:")
                    for issue in problematic:
                        print(f"      - {issue}")
        
        if default_content:
            print(f"\n📄 {default_theme}/functions.php (working theme)")
            analysis = analyze_functions_structure(default_content, default_theme)
            if analysis:
                print(f"   Length: {analysis['length']} bytes")
                print(f"   Has after_setup_theme: {analysis['has_setup']}")
                print(f"   Has template_include: {analysis['has_template_include']}")
                print(f"   Has template_redirect: {analysis['has_template_redirect']}")
                print(f"   Has output buffering: {analysis['has_output_buffering']}")
                print(f"   Has early exits: {analysis['has_early_exits']}")
        
        # Save both for comparison
        if problematic_content:
            local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_functions_php.txt"
            local_file.write_text(problematic_content, encoding='utf-8')
            print(f"\n✅ Saved problematic functions.php to: {local_file}")
        
        if default_content:
            local_file = Path(__file__).parent.parent / "docs" / "default_theme_functions_php.txt"
            local_file.write_text(default_content, encoding='utf-8')
            print(f"✅ Saved default theme functions.php to: {local_file}")
        
        return 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())






