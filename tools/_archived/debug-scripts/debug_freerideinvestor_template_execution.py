#!/usr/bin/env python3
"""
Debug freerideinvestor.com Template Execution
============================================

Adds debug output to templates to identify where execution stops.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def add_debug_to_home_php(deployer):
    """Add debug output to home.php to track execution."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    home_php = f"{remote_path}/wp-content/themes/{theme_name}/home.php"
    
    print("=" * 70)
    print("ADDING DEBUG OUTPUT TO HOME.PHP")
    print("=" * 70)
    
    # Read current content
    current_content = deployer.execute_command(f"cat {home_php}")
    
    # Add debug markers
    debug_home = """<?php
/**
 * The homepage template for displaying blog posts
 *
 * @package freerideinvestor-modern
 */

// DEBUG: Template loaded
error_log('DEBUG: home.php template loaded');

get_header();

// DEBUG: Header loaded
error_log('DEBUG: Header loaded, about to open main');
?>

<!-- DEBUG MARKER: Before main tag -->
<main id="main" class="site-main home">
    <!-- DEBUG MARKER: Main tag opened -->
    <?php
    // DEBUG: Before have_posts check
    error_log('DEBUG: Before have_posts check');
    
    global $wp_query;
    error_log('DEBUG: wp_query->post_count = ' . $wp_query->post_count);
    error_log('DEBUG: have_posts() = ' . (have_posts() ? 'true' : 'false'));
    
    if (have_posts()) :
        error_log('DEBUG: have_posts() is true, entering loop');
        ?>
        <div class="posts-container">
            <!-- DEBUG MARKER: Posts container opened -->
            <?php
            $post_count = 0;
            while (have_posts()) :
                the_post();
                $post_count++;
                error_log("DEBUG: Processing post #{$post_count}: " . get_the_title());
                get_template_part('template-parts/content', get_post_format());
            endwhile;
            error_log("DEBUG: Loop complete, processed {$post_count} posts");
            ?>
        </div>
        
        <?php
        the_posts_navigation();
        error_log('DEBUG: Posts navigation added');
    else :
        error_log('DEBUG: have_posts() is false, showing no-content');
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
    <!-- DEBUG MARKER: End of main content -->
</main>
<!-- DEBUG MARKER: Main tag closed -->

<?php
error_log('DEBUG: About to load footer');
get_footer();
error_log('DEBUG: Footer loaded, template complete');
"""
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_home_debug.php"
    local_file.parent.mkdir(parents=True, exist_ok=True)
    local_file.write_text(debug_home, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, home_php)
    
    if success:
        print("✅ Debug version of home.php deployed")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(home_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
        return True
    else:
        print("❌ Failed to deploy debug version")
        return False


def check_error_logs(deployer):
    """Check error logs for debug output."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("\n" + "=" * 70)
    print("CHECKING ERROR LOGS FOR DEBUG OUTPUT")
    print("=" * 70)
    
    # Check debug.log
    debug_log = f"{remote_path}/wp-content/debug.log"
    log_exists = deployer.execute_command(f"test -f {debug_log} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in log_exists:
        print("✅ debug.log exists")
        # Get last 50 lines
        log_content = deployer.execute_command(f"tail -50 {debug_log}")
        print("\nLast 50 lines of debug.log:")
        print(log_content)
        
        # Check for our debug messages
        if "DEBUG: home.php" in log_content:
            print("\n✅ Debug messages found in log!")
        else:
            print("\n⚠️  No debug messages found - template may not be executing")
    else:
        print("⚠️  debug.log does not exist")
        print("   Enable debug mode in wp-config.php if not already enabled")


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        # Add debug output
        if add_debug_to_home_php(deployer):
            print("\n✅ Debug version deployed")
            print("\n📋 Next steps:")
            print("   1. Visit https://freerideinvestor.com to trigger template execution")
            print("   2. Check error logs for debug output")
            print("   3. Review debug markers in HTML source")
            
            # Check existing logs
            check_error_logs(deployer)
        
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






