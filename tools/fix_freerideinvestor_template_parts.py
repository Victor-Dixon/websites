#!/usr/bin/env python3
"""
Fix freerideinvestor.com Template Parts
=======================================

Checks and fixes missing template-parts/content.php that index.php requires.

Agent-8: SSOT & System Integration Specialist
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    print("🔧 Fixing freerideinvestor.com template parts...\n")
    
    configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    try:
        wp_path = "/home/u996867598/domains/freerideinvestor.com/public_html"
        theme = "freerideinvestor-modern"
        parts_dir = f"{wp_path}/wp-content/themes/{theme}/template-parts"
        
        # Check if template-parts directory exists
        check = deployer.execute_command(f"test -d {parts_dir} && echo EXISTS || echo MISSING")
        print(f"Template-parts directory: {check.strip()}")
        
        if "MISSING" in check:
            print(f"  🔧 Creating template-parts directory...")
            deployer.execute_command(f"mkdir -p {parts_dir}")
        
        # Check for content.php
        content_php = f"{parts_dir}/content.php"
        check = deployer.execute_command(f"test -f {content_php} && echo EXISTS || echo MISSING")
        print(f"content.php: {check.strip()}")
        
        if "MISSING" in check:
            print(f"  🔧 Creating content.php template part...")
            
            # Create a basic content.php template
            content_template = """<?php
/**
 * Template part for displaying posts
 *
 * @package freerideinvestor-modern
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;
        ?>
        
        <div class="entry-meta">
            <span class="posted-on">
                <?php echo get_the_date(); ?>
            </span>
        </div>
    </header>
    
    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();
        else :
            the_excerpt();
        endif;
        ?>
    </div>
    
    <footer class="entry-footer">
        <?php
        if (!is_singular()) :
            echo '<a href="' . esc_url(get_permalink()) . '" class="read-more">Read More</a>';
        endif;
        ?>
    </footer>
</article>
"""
            
            # Write file using echo (since we don't have write_file method)
            # Escape the content properly
            escaped_content = content_template.replace('$', '\\$').replace('"', '\\"').replace('`', '\\`')
            command = f'cat > {content_php} << "EOF"\n{content_template}\nEOF'
            
            result = deployer.execute_command(command)
            if result:
                print(f"  ✅ Created content.php")
            else:
                # Alternative: use printf
                lines = content_template.split('\n')
                for i, line in enumerate(lines):
                    escaped_line = line.replace('$', '\\$').replace('"', '\\"').replace('`', '\\`').replace("'", "'\\''")
                    if i == 0:
                        deployer.execute_command(f"echo '{escaped_line}' > {content_php}")
                    else:
                        deployer.execute_command(f"echo '{escaped_line}' >> {content_php}")
                print(f"  ✅ Created content.php (alternative method)")
        else:
            print(f"  ✅ content.php already exists")
        
        # Verify file was created
        verify = deployer.execute_command(f"test -f {content_php} && echo EXISTS || echo MISSING")
        if "EXISTS" in verify:
            print(f"\n✅ Template part fix complete!")
            print(f"   File: {content_php}")
        else:
            print(f"\n⚠️  File creation may have failed")
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    main()


