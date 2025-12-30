#!/usr/bin/env python3
"""
Check Which Template WordPress is Actually Using
================================================

Determines which template file WordPress is actually loading for the homepage.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def add_template_debug_to_functions(deployer):
    """Add template debug hook to functions.php."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("=" * 70)
    print("ADDING TEMPLATE DEBUG TO FUNCTIONS.PHP")
    print("=" * 70)
    
    # Read current content
    current_content = deployer.execute_command(f"cat {functions_php}")
    
    # Check if debug hook already exists
    if "template_include_debug" in current_content:
        print("⚠️  Debug hook already exists")
        return True
    
    # Add debug hook at the end
    debug_hook = """

// DEBUG: Template usage tracking
add_filter('template_include', function($template) {
    error_log('DEBUG: WordPress template_include filter called');
    error_log('DEBUG: Template path: ' . $template);
    
    // Extract template name
    $template_name = basename($template);
    error_log('DEBUG: Template file: ' . $template_name);
    
    return $template;
}, 999);

// DEBUG: Check which template is being used
add_action('wp', function() {
    global $template;
    error_log('DEBUG: wp action fired');
    if (isset($template)) {
        error_log('DEBUG: Global $template = ' . $template);
    }
    
    // Check template hierarchy
    if (is_front_page()) {
        error_log('DEBUG: is_front_page() = true');
    }
    if (is_home()) {
        error_log('DEBUG: is_home() = true');
    }
}, 1);
"""
    
    # Append to functions.php
    new_content = current_content + debug_hook
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "functions_debug.php"
    local_file.write_text(new_content, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, functions_php)
    
    if success:
        print("✅ Debug hooks added to functions.php")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(functions_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
        return True
    else:
        print("❌ Failed to deploy debug version")
        return False


def check_template_files_existence(deployer):
    """Check which template files exist and their priorities."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    theme_path = f"{remote_path}/wp-content/themes/{theme_name}"
    
    print("\n" + "=" * 70)
    print("TEMPLATE FILES EXISTENCE CHECK")
    print("=" * 70)
    
    # WordPress template hierarchy for homepage (show_on_front = posts)
    templates = [
        ("front-page.php", "Used for static page homepage (NOT for posts)"),
        ("home.php", "Used for blog posts homepage"),
        ("index.php", "Fallback template"),
    ]
    
    for template, description in templates:
        template_path = f"{theme_path}/{template}"
        exists = deployer.execute_command(f"test -f {template_path} && echo 'EXISTS' || echo 'MISSING'")
        status = "✅" if "EXISTS" in exists else "❌"
        print(f"{status} {template:20s} - {description}")
        
        if "EXISTS" in exists:
            # Check file size
            size = deployer.execute_command(f"wc -c < {template_path}").strip()
            print(f"      Size: {size} bytes")


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        # Check template files
        check_template_files_existence(deployer)
        
        # Add debug hooks
        if add_template_debug_to_functions(deployer):
            print("\n✅ Debug hooks added")
            print("\n📋 Next steps:")
            print("   1. Visit https://freerideinvestor.com")
            print("   2. Check debug.log for template_include messages")
            print("   3. This will show which template WordPress is actually using")
        
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






