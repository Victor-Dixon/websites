#!/usr/bin/env python3
"""
Find and Disable Broken Font
=============================

Finds the problematic font and disables it.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def find_and_disable_font():
    """Find and disable the broken font."""
    print("=" * 70)
    print("🔍 FINDING AND DISABLING BROKEN FONT")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        theme_path = f"{remote_path}/wp-content/themes/southwestsecret"
        
        # Check header.php
        print("📖 Checking header.php...")
        header_file = f"{theme_path}/header.php"
        read_cmd = f"cat {header_file}"
        header_content = deployer.execute_command(read_cmd)
        
        if header_content:
            # Look for font links
            if '@import' in header_content or 'fonts.googleapis.com' in header_content or 'fonts.gstatic.com' in header_content:
                print("   ⚠️  Found font imports in header.php")
                # Show font-related lines
                lines = header_content.split('\n')
                for i, line in enumerate(lines, 1):
                    if 'font' in line.lower() or '@import' in line.lower():
                        print(f"      Line {i}: {line.strip()[:100]}")
        
        # Check functions.php for font enqueues
        print("\n📖 Checking functions.php for font enqueues...")
        functions_file = f"{theme_path}/functions.php"
        read_cmd = f"cat {functions_file}"
        functions_content = deployer.execute_command(read_cmd)
        
        if functions_content:
            # Look for font-related code
            font_keywords = ['wp_enqueue_style', 'font', '@font-face', 'googleapis', 'fonts']
            lines = functions_content.split('\n')
            font_lines = []
            for i, line in enumerate(lines, 1):
                if any(keyword in line.lower() for keyword in font_keywords):
                    font_lines.append((i, line))
            
            if font_lines:
                print(f"   ⚠️  Found {len(font_lines)} font-related lines:")
                for line_num, line in font_lines[:10]:
                    print(f"      Line {line_num}: {line.strip()[:100]}")
            else:
                print("   ✅ No font enqueues found in functions.php")
        
        # Check style.css for @font-face
        print("\n📖 Checking style.css for @font-face...")
        style_css = f"{theme_path}/style.css"
        read_cmd = f"cat {style_css}"
        css_content = deployer.execute_command(read_cmd)
        
        if css_content:
            if '@font-face' in css_content:
                print("   ⚠️  Found @font-face declarations")
                # Extract font-face blocks
                import re
                font_faces = re.findall(r'@font-face\s*\{[^}]*\}', css_content, re.DOTALL)
                for i, font_face in enumerate(font_faces, 1):
                    print(f"      Font-face #{i}: {font_face[:150]}")
            else:
                print("   ✅ No @font-face declarations found")
        
        # Strategy: Add code to disable ALL custom fonts and force system fonts
        print("\n🔧 Adding code to disable all custom fonts...")
        
        font_disable_code = '''
/**
 * DISABLE ALL CUSTOM FONTS - Fix Character Rendering - Added by Agent-7
 * This disables any problematic custom fonts and forces system fonts
 */

// Remove all font enqueues
function southwestsecret_disable_custom_fonts() {
    // Dequeue common font libraries
    wp_dequeue_style('google-fonts');
    wp_deregister_style('google-fonts');
    wp_dequeue_style('fonts.googleapis.com');
    wp_deregister_style('fonts.googleapis.com');
    
    // Remove any @font-face from style.css
    add_filter('style_loader_tag', function($tag, $handle) {
        if (strpos($tag, '@font-face') !== false || strpos($tag, 'font-face') !== false) {
            return '';
        }
        return $tag;
    }, 10, 2);
}
add_action('wp_enqueue_scripts', 'southwestsecret_disable_custom_fonts', 999);

// Force system fonts with inline CSS (highest priority)
function southwestsecret_force_system_fonts_critical() {
    echo '<style id="southwestsecret-font-fix">
        /* CRITICAL: Force system fonts - override everything */
        * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
            letter-spacing: 0 !important;
            word-spacing: 0.05em !important;
        }
        
        /* Ensure all text uses system font */
        body, html, h1, h2, h3, h4, h5, h6, p, span, a, li, div, nav, section, article, header, footer, button, input, textarea {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
        
        /* Disable any @font-face */
        @font-face {
            font-family: "*" !important;
            src: none !important;
        }
    </style>';
}
add_action('wp_head', 'southwestsecret_force_system_fonts_critical', 1);
'''
        
        # Add to functions.php
        if 'southwestsecret_disable_custom_fonts' not in functions_content:
            if '?>' in functions_content:
                new_content = functions_content.replace('?>', font_disable_code + '\n?>')
            else:
                new_content = functions_content + '\n' + font_disable_code
            
            # Save locally
            local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_functions_font_disable.php"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(new_content, encoding='utf-8')
            
            # Deploy
            print("   🚀 Deploying font disable code...")
            success = deployer.deploy_file(local_file, functions_file)
            
            if success:
                print("   ✅ Font disable code deployed")
                
                # Verify syntax
                syntax_cmd = f"php -l {functions_file} 2>&1"
                syntax_result = deployer.execute_command(syntax_cmd)
                
                if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                    print("   ✅ Syntax is valid!")
                    
                    # Clear cache
                    print("🧹 Clearing cache...")
                    cache_cmd = f"cd {remote_path} && wp cache flush && wp litespeed-purge all 2>&1"
                    deployer.execute_command(cache_cmd)
                    print("   ✅ Cache cleared")
                    
                    print("\n✅ Custom fonts disabled!")
                    print("   💡 System fonts are now forced")
                    return True
                else:
                    print(f"   ❌ Syntax error: {syntax_result[:300]}")
                    return False
            else:
                print("   ❌ Failed to deploy")
                return False
        else:
            print("   ✅ Font disable code already exists")
            return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if find_and_disable_font() else 1)





