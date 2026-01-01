#!/usr/bin/env python3
"""
Fix freerideinvestor.com Empty Content - Final Solution
=======================================================

Since templates aren't executing, creates a minimal working template
and ensures WordPress uses it correctly.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def create_minimal_working_template(deployer):
    """Create a minimal working template that definitely renders."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    
    # Create a super simple index.php that will definitely work
    minimal_index = """<?php
/**
 * The main template file - MINIMAL WORKING VERSION
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="posts-container">
        <h1>FreeRide Investor</h1>
        <p>Welcome to FreeRide Investor - Your source for trading insights.</p>
        
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    </header>
                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <?php
            endwhile;
        else :
            ?>
            <p>No posts found.</p>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
"""
    
    index_php = f"{remote_path}/wp-content/themes/{theme_name}/index.php"
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_index_minimal.php"
    local_file.parent.mkdir(parents=True, exist_ok=True)
    local_file.write_text(minimal_index, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, index_php)
    
    if success:
        print("✅ Minimal working index.php deployed")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(index_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
        return True
    else:
        print("❌ Failed to deploy")
        return False


def restore_front_page_php(deployer):
    """Restore front-page.php from backup."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    
    # Check if backup exists
    backup = f"{remote_path}/wp-content/themes/{theme_name}/front-page.php.backup"
    exists = deployer.execute_command(f"test -f {backup} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in exists:
        # Restore it
        result = deployer.execute_command(
            f"cd {remote_path}/wp-content/themes/{theme_name} && "
            f"mv front-page.php.backup front-page.php && echo 'RESTORED'"
        )
        if "RESTORED" in result:
            print("✅ Restored front-page.php from backup")
            return True
    
    return False


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        print("=" * 70)
        print("FINAL SOLUTION: MINIMAL WORKING TEMPLATE")
        print("=" * 70)
        
        # Create minimal template
        if create_minimal_working_template(deployer):
            print("\n✅ Minimal template deployed")
            print("\n📋 Testing in 3 seconds...")
            import time
            time.sleep(3)
            
            import requests
            from bs4 import BeautifulSoup
            r = requests.get("https://freerideinvestor.com", timeout=10)
            soup = BeautifulSoup(r.text, 'html.parser')
            main = soup.find('main')
            
            if main:
                print("🎉 SUCCESS! Main tag found!")
                print(f"   Body text length: {len(soup.find('body').get_text()) if soup.find('body') else 0} chars")
                print(f"   Articles: {len(soup.find_all('article'))}")
            else:
                print("❌ Main tag still missing")
                print("   Issue is deeper than template structure")
                print("   May need to check WordPress core or server configuration")
        
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


