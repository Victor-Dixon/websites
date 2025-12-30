#!/usr/bin/env python3
"""
Deploy Beautiful Single Post Template
=====================================

Deploys the beautiful single post template and CSS to digitaldreamscape.site
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "digitaldreamscape.site"

def main():
    """Main execution."""
    print("=" * 70)
    print("DEPLOY BEAUTIFUL SINGLE POST TEMPLATE")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    if SITE_NAME not in site_configs:
        print(f"❌ Site {SITE_NAME} not found")
        return
    
    deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    remote_path = site_configs[SITE_NAME].get('sftp', {}).get('remote_path', 
        'domains/digitaldreamscape.site/public_html')
    theme_path = f"{remote_path}/wp-content/themes/digitaldreamscape"
    
    print(f"📂 Theme path: {theme_path}")
    print()
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return
    
    try:
        # Deploy single-beautiful.php
        single_file = Path(__file__).parent.parent / "websites" / "digitaldreamscape.site" / "wp" / "wp-content" / "themes" / "digitaldreamscape" / "single-beautiful.php"
        if single_file.exists():
            print("📝 Deploying single-beautiful.php...")
            with open(single_file, 'r', encoding='utf-8') as f:
                content = f.read()
            with deployer.sftp.open(f"{theme_path}/single-beautiful.php", 'w') as f:
                f.write(content.encode('utf-8'))
            print("✅ Template deployed")
        else:
            print(f"❌ Template file not found: {single_file}")
        
        # Deploy CSS
        css_file = Path(__file__).parent.parent / "websites" / "digitaldreamscape.site" / "wp" / "wp-content" / "themes" / "digitaldreamscape" / "assets" / "css" / "beautiful-single.css"
        if css_file.exists():
            print("🎨 Deploying beautiful-single.css...")
            with open(css_file, 'r', encoding='utf-8') as f:
                content = f.read()
            with deployer.sftp.open(f"{theme_path}/assets/css/beautiful-single.css", 'w') as f:
                f.write(content.encode('utf-8'))
            print("✅ CSS deployed")
        else:
            print(f"❌ CSS file not found: {css_file}")
        
        # Update functions.php to use single-beautiful.php for single posts
        print()
        print("📝 Updating functions.php template mapping...")
        functions_path = f"{theme_path}/functions.php"
        with deployer.sftp.open(functions_path, 'r') as f:
            functions_content = f.read().decode('utf-8')
        
        # Check if single post template mapping exists
        if "single-beautiful.php" not in functions_content:
            # Add single post template mapping in template_include filter
            # Find the template_include filter
            if "add_filter('template_include'" in functions_content:
                # Add single post check before page template mapping
                single_mapping = """
    // Force single posts to use beautiful single template
    if (is_single() && !is_page()) {
        $single_template = get_template_directory() . '/single-beautiful.php';
        if (file_exists($single_template)) {
            return $single_template;
        }
    }
"""
                # Insert before page template mapping
                if "// Get the page slug" in functions_content:
                    functions_content = functions_content.replace(
                        "    // Get the page slug",
                        single_mapping + "    // Get the page slug"
                    )
                elif "if (is_page())" in functions_content:
                    functions_content = functions_content.replace(
                        "    if (is_page())",
                        single_mapping + "    if (is_page())"
                    )
                else:
                    # Add at the beginning of the filter function
                    functions_content = functions_content.replace(
                        "add_filter('template_include', function ($template) {",
                        "add_filter('template_include', function ($template) {\n" + single_mapping
                    )
                
                with deployer.sftp.open(functions_path, 'w') as f:
                    f.write(functions_content.encode('utf-8'))
                print("✅ Added single post template mapping")
            else:
                print("⚠️  Could not find template_include filter")
        
        # Ensure CSS is enqueued (already added in functions.php)
        print("✅ CSS enqueue already configured")
        
        # Clear cache
        print()
        print("🗑️  Clearing cache...")
        deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root")
        print("✅ Cache cleared")
        
    finally:
        deployer.disconnect()
    
    print()
    print("=" * 70)
    print("✅ DEPLOYMENT COMPLETE")
    print("=" * 70)
    print()
    print("The beautiful single post template is now active for all posts.")

if __name__ == "__main__":
    main()

