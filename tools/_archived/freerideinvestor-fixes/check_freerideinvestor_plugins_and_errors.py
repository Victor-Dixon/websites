#!/usr/bin/env python3
"""
Check freerideinvestor.com Plugins and Error Logs
=================================================

Checks for plugin conflicts and WordPress error logs that might explain
why templates aren't executing.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_active_plugins(deployer):
    """Check active plugins that might interfere with templates."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("=" * 70)
    print("CHECKING ACTIVE PLUGINS")
    print("=" * 70)
    
    plugins = deployer.execute_command(
        f"cd {remote_path} && wp plugin list --status=active --allow-root 2>/dev/null"
    )
    
    if plugins:
        print("Active plugins:")
        print(plugins)
        
        # Check for problematic plugins
        problematic = [
            "page-builder",
            "visual-composer",
            "elementor",
            "beaver-builder",
            "divi",
            "template",
            "customizer"
        ]
        
        issues = []
        for plugin in problematic:
            if plugin.lower() in plugins.lower():
                issues.append(plugin)
        
        if issues:
            print(f"\n⚠️  Potentially problematic plugins found: {', '.join(issues)}")
            print("   These might override WordPress template system")
        else:
            print("\n✅ No obviously problematic plugins found")
    else:
        print("⚠️  Could not retrieve plugin list (WP-CLI may not be available)")


def check_error_logs_detailed(deployer):
    """Check WordPress error logs for fatal errors."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("\n" + "=" * 70)
    print("CHECKING ERROR LOGS FOR FATAL ERRORS")
    print("=" * 70)
    
    # Check debug.log
    debug_log = f"{remote_path}/wp-content/debug.log"
    log_exists = deployer.execute_command(f"test -f {debug_log} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in log_exists:
        print("✅ debug.log exists")
        # Get last 100 lines
        log_content = deployer.execute_command(f"tail -100 {debug_log} 2>/dev/null")
        
        if log_content and log_content.strip():
            print("\nLast 100 lines of debug.log:")
            print(log_content[-2000:])  # Last 2000 chars
            
            # Check for fatal errors
            fatal_keywords = ["Fatal error", "Parse error", "syntax error", "Call to undefined"]
            found_fatals = []
            for keyword in fatal_keywords:
                if keyword.lower() in log_content.lower():
                    found_fatals.append(keyword)
            
            if found_fatals:
                print(f"\n⚠️  Found potential fatal errors: {', '.join(found_fatals)}")
            else:
                print("\n✅ No fatal errors found in recent logs")
        else:
            print("⚠️  debug.log is empty")
    else:
        print("⚠️  debug.log does not exist")
        print("   WordPress debug mode may not be enabled")
    
    # Check PHP error log
    php_error_log = f"{remote_path}/error_log"
    php_log_exists = deployer.execute_command(f"test -f {php_error_log} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in php_log_exists:
        print("\n✅ PHP error_log exists")
        php_log_content = deployer.execute_command(f"tail -50 {php_error_log} 2>/dev/null")
        if php_log_content and php_log_content.strip():
            print("\nLast 50 lines of PHP error_log:")
            print(php_log_content[-1000:])  # Last 1000 chars


def check_functions_php_hooks(deployer):
    """Check functions.php for hooks that might suppress output."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("\n" + "=" * 70)
    print("CHECKING FUNCTIONS.PHP FOR OUTPUT SUPPRESSION")
    print("=" * 70)
    
    content = deployer.execute_command(f"cat {functions_php}")
    
    # Check for problematic patterns
    problematic_patterns = [
        ("ob_start", "Output buffering"),
        ("ob_end", "Output buffering end"),
        ("wp_die", "WordPress die"),
        ("die()", "PHP die"),
        ("exit()", "PHP exit"),
        ("template_redirect", "Template redirect hook"),
        ("template_include", "Template include filter"),
        ("remove_action('wp_head'", "Removed wp_head"),
        ("remove_action('wp_footer'", "Removed wp_footer"),
    ]
    
    issues = []
    for pattern, description in problematic_patterns:
        if pattern in content:
            # Count occurrences
            count = content.count(pattern)
            issues.append((description, count, pattern))
    
    if issues:
        print("⚠️  Found potentially problematic patterns:")
        for desc, count, pattern in issues:
            print(f"   - {desc}: {count} occurrence(s)")
            if pattern in ["template_redirect", "template_include"]:
                print("      ⚠️  This might be redirecting or modifying template loading")
    else:
        print("✅ No obvious output suppression patterns found")


def check_template_hierarchy_via_wp(deployer):
    """Use WordPress to determine which template is actually being used."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("\n" + "=" * 70)
    print("VERIFYING TEMPLATE HIERARCHY VIA WORDPRESS")
    print("=" * 70)
    
    # Check homepage setting
    show_on_front = deployer.execute_command(
        f"cd {remote_path} && wp option get show_on_front --allow-root 2>/dev/null || echo 'posts'"
    ).strip()
    
    print(f"Homepage setting: {show_on_front}")
    
    # Check if we can query template info
    # WordPress doesn't have a direct CLI command, but we can check template files
    theme_name = "freerideinvestor-modern"
    theme_path = f"{remote_path}/wp-content/themes/{theme_name}"
    
    print("\nTemplate hierarchy for blog posts homepage:")
    print("  1. front-page.php (if exists) - ONLY for static page")
    print("  2. home.php (if exists) - For blog posts")
    print("  3. index.php (fallback)")
    
    # Check which exists
    templates = ["front-page.php", "home.php", "index.php"]
    for template in templates:
        template_path = f"{theme_path}/{template}"
        exists = deployer.execute_command(f"test -f {template_path} && echo 'EXISTS' || echo 'MISSING'")
        status = "✅" if "EXISTS" in exists else "❌"
        print(f"  {status} {template}")
        
        # Check if front-page.php is taking priority incorrectly
        if template == "front-page.php" and "EXISTS" in exists and show_on_front == "posts":
            print("     ⚠️  front-page.php exists but homepage is set to posts")
            print("     ⚠️  WordPress should use home.php, not front-page.php")


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        check_active_plugins(deployer)
        check_error_logs_detailed(deployer)
        check_functions_php_hooks(deployer)
        check_template_hierarchy_via_wp(deployer)
        
        print("\n" + "=" * 70)
        print("SUMMARY")
        print("=" * 70)
        print("Review the findings above to identify the root cause.")
        print("Common issues:")
        print("  - Plugin overriding template system")
        print("  - Fatal error in functions.php or template")
        print("  - Output buffering suppressing content")
        print("  - Template redirect hook changing template")
        
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






