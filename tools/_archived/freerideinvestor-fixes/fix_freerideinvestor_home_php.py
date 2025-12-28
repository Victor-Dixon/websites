#!/usr/bin/env python3
"""
Fix freerideinvestor.com home.php for Blog Posts Homepage
=========================================================

Since homepage is set to "posts", WordPress uses home.php (not front-page.php).
Fixes home.php to properly display blog posts.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_home_php(deployer):
    """Fix home.php to properly display blog posts."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    home_php = f"{remote_path}/wp-content/themes/{theme_name}/home.php"
    
    print("=" * 70)
    print("FIXING HOME.PHP FOR BLOG POSTS HOMEPAGE")
    print("=" * 70)
    
    # Read current content
    current_content = deployer.execute_command(f"cat {home_php}")
    print(f"Current home.php length: {len(current_content)} bytes")
    
    # Create proper home.php for blog posts
    # Footer starts with </main>, so we need to open <main> here
    proper_home = """<?php
/**
 * The homepage template for displaying blog posts
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main home">
    <?php
    if (have_posts()) :
        ?>
        <div class="posts-container">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', get_post_format());
            endwhile;
            ?>
        </div>
        
        <?php
        the_posts_navigation();
    else :
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
</main>

<?php
get_footer();
"""
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_home_fixed.php"
    local_file.parent.mkdir(parents=True, exist_ok=True)
    local_file.write_text(proper_home, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, home_php)
    
    if success:
        print("✅ home.php fixed and deployed")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(home_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
            if syntax_result.get('line_number'):
                print(f"   Line {syntax_result.get('line_number')}")
        return True
    else:
        print("❌ Failed to deploy home.php")
        return False


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        fix_success = fix_home_php(deployer)
        
        print("\n" + "=" * 70)
        print("NEXT STEPS")
        print("=" * 70)
        print("1. Test the site - content should now display")
        print("2. WordPress uses home.php for blog posts homepage")
        print("3. front-page.php is only used for static page homepage")
        
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






