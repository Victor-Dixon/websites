#!/usr/bin/env python3
"""
Fix freerideinvestor.com Template Structure
==========================================

Fixes the template structure to ensure main content renders correctly.
The footer.php starts with </main>, so templates need to open <main> before footer is called.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_front_page_php(deployer):
    """Fix front-page.php to properly open <main> before content."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    front_page_php = f"{remote_path}/wp-content/themes/{theme_name}/front-page.php"
    
    # Read current content
    current_content = deployer.execute_command(f"cat {front_page_php}")
    
    print("=" * 70)
    print("FIXING FRONT-PAGE.PHP")
    print("=" * 70)
    print(f"Current content length: {len(current_content)} bytes")
    
    # Check if it already has <main> opening
    if "<main" in current_content and "have_posts" in current_content:
        print("✅ front-page.php already has <main> and loop")
        # But verify it's structured correctly
        if current_content.find("<main") < current_content.find("have_posts"):
            print("✅ Structure is correct")
            return True
        else:
            print("⚠️  Structure issue - fixing...")
    
    # Create proper front-page.php
    # Since footer.php starts with </main>, we need to open <main> here
    proper_front_page = """<?php
/**
 * The front page template file
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main front-page">
    <?php
    // Display latest posts or static content
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content', 'front-page');
        endwhile;
    else :
        // If no posts, show welcome message
        ?>
        <article class="no-content">
            <div class="entry-content">
                <h1>Welcome to FreeRide Investor</h1>
                <p>Your source for trading insights and investment strategies.</p>
                <p>Content coming soon...</p>
            </div>
        </article>
        <?php
    endif;
    ?>
</main>

<?php
get_footer();
"""
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_front_page_fixed_v2.php"
    local_file.parent.mkdir(parents=True, exist_ok=True)
    local_file.write_text(proper_front_page, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, front_page_php)
    
    if success:
        print("✅ front-page.php fixed and deployed")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(front_page_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
            if syntax_result.get('line_number'):
                print(f"   Line {syntax_result.get('line_number')}")
        return True
    else:
        print("❌ Failed to deploy front-page.php")
        return False


def verify_template_usage(deployer):
    """Verify which template WordPress is actually using."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("\n" + "=" * 70)
    print("VERIFYING TEMPLATE USAGE")
    print("=" * 70)
    
    # Check homepage setting
    show_on_front = deployer.execute_command(
        f"cd {remote_path} && wp option get show_on_front --allow-root 2>/dev/null || echo 'posts'"
    ).strip()
    
    print(f"Homepage setting: {show_on_front}")
    
    if show_on_front == "posts":
        print("WordPress will use:")
        print("  1. front-page.php (if exists) ← Should be used")
        print("  2. home.php (if exists)")
        print("  3. index.php (fallback)")
        
        # Check if front-page.php exists
        theme_name = "freerideinvestor-modern"
        front_page = f"{remote_path}/wp-content/themes/{theme_name}/front-page.php"
        exists = deployer.execute_command(f"test -f {front_page} && echo 'EXISTS' || echo 'MISSING'")
        
        if "EXISTS" in exists:
            print("✅ front-page.php exists - WordPress should use it")
            # Check if it has proper structure
            content = deployer.execute_command(f"cat {front_page}")
            if "<main" in content and "have_posts" in content:
                print("✅ front-page.php has proper structure")
            else:
                print("⚠️  front-page.php structure needs fixing")
        else:
            print("❌ front-page.php missing - WordPress will use home.php or index.php")


def test_with_default_theme(deployer):
    """Test if the issue persists with a default WordPress theme."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("\n" + "=" * 70)
    print("TESTING WITH DEFAULT THEME")
    print("=" * 70)
    
    # List available themes
    themes = deployer.execute_command(
        f"cd {remote_path} && wp theme list --allow-root 2>/dev/null | head -10"
    )
    
    print("Available themes:")
    print(themes)
    
    # Check if a default theme exists
    default_themes = ["twentytwentyfour", "twentytwentythree", "twentytwentytwo", "twentytwentyone"]
    current_theme = deployer.execute_command(
        f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>/dev/null"
    ).strip()
    
    print(f"\nCurrent active theme: {current_theme}")
    
    # Ask user if they want to switch (we'll just show the option)
    print("\n💡 To test with default theme, run:")
    for theme in default_themes:
        print(f"   wp theme activate {theme} --path={remote_path} --allow-root")
    print("\n⚠️  Note: This will change the active theme temporarily for testing")


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        # Fix front-page.php structure
        fix_success = fix_front_page_php(deployer)
        
        # Verify template usage
        verify_template_usage(deployer)
        
        # Show default theme testing option
        test_with_default_theme(deployer)
        
        print("\n" + "=" * 70)
        print("NEXT STEPS")
        print("=" * 70)
        print("1. Test the site to see if content now displays")
        print("2. If still empty, check browser console for JavaScript errors")
        print("3. If still empty, test with default theme to isolate issue")
        print("4. Check CSS for display:none or visibility:hidden on main content")
        
        return 0 if fix_success else 1
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())


