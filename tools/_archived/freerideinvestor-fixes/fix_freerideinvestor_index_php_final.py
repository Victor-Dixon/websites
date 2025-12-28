#!/usr/bin/env python3
"""
Fix freerideinvestor.com index.php (Final Fix)
==============================================

WordPress is using index.php as fallback. Fixes it to properly render content.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_index_php(deployer):
    """Fix index.php to properly render content."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    index_php = f"{remote_path}/wp-content/themes/{theme_name}/index.php"
    
    print("=" * 70)
    print("FIXING INDEX.PHP (FINAL FIX)")
    print("=" * 70)
    
    # Create proper index.php
    # Footer starts with </main>, so we need to open <main> here
    proper_index = """<?php
/**
 * The main template file
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main">
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
// Removed get_sidebar() - sidebar.php doesn't exist
get_footer();
"""
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_index_final.php"
    local_file.parent.mkdir(parents=True, exist_ok=True)
    local_file.write_text(proper_index, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, index_php)
    
    if success:
        print("✅ index.php fixed and deployed")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(index_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
        return True
    else:
        print("❌ Failed to deploy index.php")
        return False


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        if fix_index_php(deployer):
            print("\n✅ index.php fixed")
            print("\n📋 Testing site now...")
            import requests
            import time
            time.sleep(2)
            r = requests.get("https://freerideinvestor.com", timeout=10)
            from bs4 import BeautifulSoup
            soup = BeautifulSoup(r.text, 'html.parser')
            main = soup.find('main')
            print(f"   Main tag: {'✅ Found' if main else '❌ Missing'}")
            print(f"   Body text: {len(soup.find('body').get_text()) if soup.find('body') else 0} chars")
        
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

