#!/usr/bin/env python3
"""
Add Missing Alt Text to Images Across All WordPress Sites
==========================================================

Adds missing alt text to images across all WordPress sites for accessibility.
Uses WordPress filters to automatically add alt text when missing.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


# Sites to update (all WordPress sites)
SITES = [
    "ariajet.site",
    "crosbyultimateevents.com",
    "houstonsipqueen.com",
    "digitaldreamscape.site",
    "freerideinvestor.com",
    "prismblossom.online",
    "southwestsecret.com",
    "tradingrobotplug.com",
    "weareswarm.online",
    "weareswarm.site",
]


def generate_alt_text_function():
    """Generate WordPress function to add missing alt text."""
    # Raw string avoids Python "invalid escape sequence" warnings from PHP regexes.
    return r'''
/**
 * Add Missing Alt Text to Images - Added by Agent-7
 * Automatically adds descriptive alt text to images that are missing it
 */

// Add alt text to images in post content when missing (regex-based approach)
function add_missing_alt_text_to_content($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Find all img tags without alt or with empty alt
    $content = preg_replace_callback(
        '/<img([^>]*?)(?:alt=["\']([^"\']*)["\'])?([^>]*?)>/i',
        function($matches) {
            $before_alt = isset($matches[1]) ? $matches[1] : '';
            $existing_alt = isset($matches[2]) ? $matches[2] : '';
            $after_alt = isset($matches[3]) ? $matches[3] : '';
            
            // If alt is empty or missing, generate alt text
            if (empty($existing_alt)) {
                // Try to get from title attribute first
                preg_match('/title=["\']([^"\']*)["\']/', $before_alt . $after_alt, $title_match);
                if (!empty($title_match[1])) {
                    $alt_text = $title_match[1];
                } else {
                    // Try to get from src filename
                    preg_match('/src=["\']([^"\']*)["\']/', $before_alt . $after_alt, $src_match);
                    if (!empty($src_match[1])) {
                        $filename = basename($src_match[1]);
                        $name_without_ext = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
                        $name_formatted = str_replace(array('-', '_'), ' ', $name_without_ext);
                        $name_formatted = ucwords($name_formatted);
                        $alt_text = !empty($name_formatted) ? $name_formatted : 'Image';
                    } else {
                        $alt_text = 'Image';
                    }
                }
                
                // Add alt attribute
                if (strpos($before_alt . $after_alt, 'alt=') !== false) {
                    // Replace existing empty alt
                    return preg_replace('/alt=["\'][^"\']*["\']/', 'alt="' . esc_attr($alt_text) . '"', $matches[0]);
                } else {
                    // Add alt attribute before closing >
                    return '<img' . $before_alt . ' alt="' . esc_attr($alt_text) . '"' . $after_alt . '>';
                }
            }
            
            return $matches[0];
        },
        $content
    );
    
    return $content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing
function add_missing_alt_text_to_thumbnails($html, $post_id, $post_thumbnail_id, $size, $attr) {
    if (empty($html)) {
        return $html;
    }
    
    // Check if alt is already set and not empty
    if (preg_match('/alt=["\']([^"\']+)["\']/', $html, $matches)) {
        if (!empty($matches[1])) {
            return $html; // Alt already exists and is not empty
        }
    }
    
    // Get alt text from attachment meta or post title
    $alt_text = '';
    if ($post_thumbnail_id) {
        $alt_text = get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true);
    }
    
    if (empty($alt_text) && $post_thumbnail_id) {
        // Try to get from attachment title
        $attachment = get_post($post_thumbnail_id);
        if ($attachment && !empty($attachment->post_title)) {
            $alt_text = $attachment->post_title;
        }
    }
    
    if (empty($alt_text) && $post_id) {
        // Fallback to post title
        $post = get_post($post_id);
        if ($post && !empty($post->post_title)) {
            $alt_text = $post->post_title . ' - Featured Image';
        }
    }
    
    if (empty($alt_text)) {
        $alt_text = 'Featured Image';
    }
    
    // Add or update alt attribute
    if (strpos($html, '<img') !== false) {
        if (preg_match('/alt=["\'][^"\']*["\']/', $html)) {
            // Replace existing empty alt
            $html = preg_replace('/alt=["\'][^"\']*["\']/', 'alt="' . esc_attr($alt_text) . '"', $html);
        } else {
            // Add alt attribute
            $html = preg_replace('/(<img[^>]+)(\s*\/?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing
function add_missing_alt_text_to_widgets($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Use same regex approach as post content
    return add_missing_alt_text_to_content($content);
}
add_filter('widget_text', 'add_missing_alt_text_to_widgets', 20);
add_filter('the_excerpt', 'add_missing_alt_text_to_widgets', 20);
'''


def add_alt_text_functionality(site_name: str):
    """Add alt text functionality to a WordPress site."""
    print(f"\n{'='*70}")
    print(f"🖼️  ADDING ALT TEXT FUNCTIONALITY: {site_name}")
    print(f"{'='*70}")
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        theme_path = f"{remote_path}/wp-content/themes"
        
        # Find active theme
        print("🔍 Finding active theme...")
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        
        site_theme_name = site_name.replace('.', '').replace('-', '')
        possible_themes = [site_theme_name, site_name.split('.')[0], 'default', 'twentytwentyfour']
        
        if themes_list:
            for line in themes_list.strip().split('\n'):
                if line.strip() and line.strip() not in possible_themes:
                    possible_themes.append(line.strip())
        
        theme_found = False
        functions_file = None
        theme_name = None
        
        for tname in possible_themes:
            functions_path = f"{theme_path}/{tname}/functions.php"
            check_cmd = f"test -f {functions_path} && echo 'exists' || echo 'not found'"
            check_result = deployer.execute_command(check_cmd)
            
            if 'exists' in check_result:
                functions_file = functions_path
                theme_name = tname
                theme_found = True
                print(f"   ✅ Found theme: {theme_name}")
                break
        
        if not theme_found:
            print("❌ Could not find theme functions.php")
            return False
        
        # Read current functions.php
        print(f"📖 Reading {functions_file}...")
        read_cmd = f"cat {functions_file}"
        functions_content = deployer.execute_command(read_cmd)
        
        if not functions_content:
            print("❌ Could not read functions.php")
            return False
        
        # Check if alt text functionality already exists
        if 'add_missing_alt_text' in functions_content or 'add_missing_alt_text_to_content' in functions_content:
            print("⚠️  Alt text functionality may already exist")
            if 'add_missing_alt_text_to_content' in functions_content and 'add_missing_alt_text_to_thumbnails' in functions_content:
                print("   ✅ Alt text functionality already added")
                return True
        
        # Generate alt text function
        alt_text_function = generate_alt_text_function()
        
        # Add to functions.php (SAFELY).
        # If the file ends with a PHP closing tag, remove it first so we never
        # accidentally append PHP code outside of PHP mode (which would get
        # printed as raw text at the top of the site).
        stripped = functions_content.rstrip()
        if stripped.endswith('?>'):
            stripped = stripped[:-2].rstrip()

        new_content = stripped + '\n' + alt_text_function + '\n'
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_with_alt_text.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying updated functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print(f"   ✅ Alt text functionality added successfully!")
            
            # Verify syntax
            print("🔍 Verifying PHP syntax...")
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ PHP syntax is valid!")
                return True
            else:
                print(f"   ⚠️  Syntax check: {syntax_result[:200]}")
                return False
        else:
            print("   ❌ Failed to deploy updated file")
            return False
            
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    print("=" * 70)
    print("🖼️  ADDING MISSING ALT TEXT TO IMAGES ACROSS ALL SITES")
    print("=" * 70)
    print()
    print(f"Sites to update: {len(SITES)}")
    print()
    
    results = {}
    
    for site_name in SITES:
        success = add_alt_text_functionality(site_name)
        results[site_name] = "✅ SUCCESS" if success else "❌ FAILED"
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 SUMMARY")
    print("=" * 70)
    print()
    
    success_count = sum(1 for r in results.values() if "SUCCESS" in r)
    
    for site_name, result in results.items():
        print(f"  {site_name}: {result}")
    
    print()
    print(f"✅ Successfully updated: {success_count}/{len(SITES)} sites")
    
    if success_count == len(SITES):
        print("🎉 All sites now have automatic alt text functionality!")
        print()
        print("💡 How it works:")
        print("   - Automatically adds alt text to images in post content when missing")
        print("   - Adds alt text to post thumbnails based on attachment or post title")
        print("   - Adds alt text to widget images when missing")
        print("   - Uses image filename or default fallback when no title available")
        return 0
    else:
        print(f"⚠️  {len(SITES) - success_count} sites failed")
        return 1


if __name__ == "__main__":
    sys.exit(main())
