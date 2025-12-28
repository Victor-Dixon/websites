#!/usr/bin/env python3
"""Check template-parts directory and create missing content templates."""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_template_parts(deployer):
    """Check if template-parts directory exists and has content templates."""
    remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    template_parts_dir = f"{remote_path}/wp-content/themes/{theme_name}/template-parts"
    
    # Check if directory exists
    dir_exists = deployer.execute_command(f"test -d {template_parts_dir} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    
    if "NOT_EXISTS" in dir_exists:
        print(f"   ⚠️  template-parts directory does not exist")
        # Create it
        deployer.execute_command(f"mkdir -p {template_parts_dir}")
        print(f"   ✅ Created template-parts directory")
    
    # Check for content.php
    content_php = f"{template_parts_dir}/content.php"
    content_exists = deployer.execute_command(f"test -f {content_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    
    if "NOT_EXISTS" in content_exists:
        print(f"   ⚠️  content.php template does not exist")
        # Create basic content template
        content_template = """<?php
/**
 * Template part for displaying posts
 *
 * @package freerideinvestor-modern
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header>

    <div class="entry-content">
        <?php
        the_content();
        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'freerideinvestor-modern'),
            'after'  => '</div>',
        ));
        ?>
    </div>
</article>
"""
        local_file = Path(__file__).parent.parent / "docs" / "content_template.php"
        local_file.write_text(content_template, encoding='utf-8')
        deployer.deploy_file(local_file, content_php)
        print(f"   ✅ Created content.php template")
    else:
        print(f"   ✅ content.php exists")
    
    # Check for content-none.php
    content_none_php = f"{template_parts_dir}/content-none.php"
    content_none_exists = deployer.execute_command(f"test -f {content_none_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    
    if "NOT_EXISTS" in content_none_exists:
        print(f"   ⚠️  content-none.php template does not exist")
        # Create basic no-content template
        content_none_template = """<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package freerideinvestor-modern
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nothing here', 'freerideinvestor-modern'); ?></h1>
    </header>

    <div class="page-content">
        <p><?php esc_html_e('It seems we can\'t find what you\'re looking for.', 'freerideinvestor-modern'); ?></p>
    </div>
</section>
"""
        local_file = Path(__file__).parent.parent / "docs" / "content_none_template.php"
        local_file.write_text(content_none_template, encoding='utf-8')
        deployer.deploy_file(local_file, content_none_php)
        print(f"   ✅ Created content-none.php template")
    else:
        print(f"   ✅ content-none.php exists")
    
    # Check for content-front-page.php
    content_front_page_php = f"{template_parts_dir}/content-front-page.php"
    content_front_page_exists = deployer.execute_command(f"test -f {content_front_page_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    
    if "NOT_EXISTS" in content_front_page_exists:
        print(f"   ⚠️  content-front-page.php template does not exist")
        # Create basic front-page content template
        content_front_page_template = """<?php
/**
 * Template part for displaying front page content
 *
 * @package freerideinvestor-modern
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        the_content();
        ?>
    </div>
</article>
"""
        local_file = Path(__file__).parent.parent / "docs" / "content_front_page_template.php"
        local_file.write_text(content_front_page_template, encoding='utf-8')
        deployer.deploy_file(local_file, content_front_page_php)
        print(f"   ✅ Created content-front-page.php template")
    else:
        print(f"   ✅ content-front-page.php exists")


def main():
    print("=" * 70)
    print("🔍 CHECKING FREERIDEINVESTOR.COM TEMPLATE PARTS")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        check_template_parts(deployer)
        print()
        print("✅ Template parts check complete")
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






