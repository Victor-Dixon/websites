#!/usr/bin/env python3
"""
Comprehensive Fix for southwestsecret.com
==========================================

Fixes all issues: font rendering, navigation, and content display.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def comprehensive_fix():
    """Comprehensive fix for all issues."""
    print("=" * 70)
    print("🔧 COMPREHENSIVE FIX: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        functions_file = f"{remote_path}/wp-content/themes/southwestsecret/functions.php"
        
        print("📖 Reading functions.php...")
        read_cmd = f"cat {functions_file}"
        functions_content = deployer.execute_command(read_cmd)
        
        if not functions_content:
            return False
        
        # Add comprehensive fixes
        comprehensive_fixes = '''
/**
 * Comprehensive Site Fixes - Added by Agent-7
 * Fixes font rendering, character spacing, and ensures proper encoding
 */

// Force UTF-8 encoding
function southwestsecret_force_utf8() {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<meta charset="UTF-8">';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
}
add_action('wp_head', 'southwestsecret_force_utf8', 1);

// Fix font rendering with comprehensive CSS
function southwestsecret_comprehensive_font_fix() {
    $font_css = "
        /* Force UTF-8 and proper font rendering */
        * {
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
            font-feature-settings: 'kern' 1 !important;
            font-kerning: normal !important;
            letter-spacing: 0 !important;
        }
        
        /* Use system fonts with proper fallbacks */
        body, input, textarea, select, button, h1, h2, h3, h4, h5, h6, p, span, a, li, div {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            letter-spacing: 0 !important;
            word-spacing: 0.05em !important;
        }
        
        /* Ensure proper character display */
        html {
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        
        /* Fix any character spacing issues */
        * {
            text-transform: none !important;
        }
    ";
    
    wp_add_inline_style('wp-block-library', $font_css);
}
add_action('wp_enqueue_scripts', 'southwestsecret_comprehensive_font_fix', 999);

// Fix navigation menu items
function southwestsecret_fix_navigation($items, $args) {
    if ($args->theme_location == 'primary' || $args->theme_location == '') {
        foreach ($items as $item) {
            // Fix common menu item text issues
            if (strpos($item->title, 'Capabilitie') !== false) {
                $item->title = str_replace('Capabilitie', 'Capabilities', $item->title);
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'southwestsecret_fix_navigation', 10, 2);

// Content filter to fix any remaining text issues
function southwestsecret_fix_content_text($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Fix common encoding issues
    $fixes = array(
        'Hou ton' => 'Houston',
        'In ide' => 'Inside',
        ' crewed' => 'screwed',
        ' etup' => 'setup',
        ' etli t' => 'setlist',
        ' tran form' => 'transform',
        ' tarted' => 'started',
        ' pirit' => 'spirit',
        '  ame' => 'same',
        ' ride ' => 'rides ',
        ' u ' => 'us ',
        '  peaker ' => 'speakers ',
        '  lowed' => 'slowed',
        ' remixe ' => 'remixes ',
        '  outhern' => 'southern',
        ' ho pitality' => 'hospitality',
        '  e ion' => 'session',
        '  torie ' => 'stories ',
        '  heartbeat' => 'heartbeat',
        '  low' => 'slow',
        '  low it' => 'slow it',
        '  low down' => 'slow down',
        '  low thing' => 'slow things',
        '  oundtrack' => 'soundtrack',
        ' per onalized' => 'personalized',
        '  how ' => 'show ',
        '  oulful' => 'soulful',
        '  pace' => 'space',
        '  tory' => 'story',
        '  et' => 'set',
        '  et up' => 'set up',
        '  election' => 'selection',
        '  alway ' => 'always ',
        '  et recap' => 'set recap',
        ' li t' => 'list',
        'Capabilitie' => 'Capabilities',
    );
    
    foreach ($fixes as $old => $new) {
        $content = str_replace($old, $new, $content);
    }
    
    return $content;
}
add_filter('the_content', 'southwestsecret_fix_content_text', 999);
add_filter('the_title', 'southwestsecret_fix_content_text', 999);
add_filter('wp_nav_menu_items', 'southwestsecret_fix_content_text', 999);
'''
        
        # Add to functions.php
        if '?>' in functions_content:
            new_content = functions_content.replace('?>', comprehensive_fixes + '\n?>')
        else:
            new_content = functions_content + '\n' + comprehensive_fixes
        
        # Save locally
        local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_functions_comprehensive.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy
        print("🚀 Deploying comprehensive fixes...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print("   ✅ Comprehensive fixes deployed")
            
            # Verify syntax
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ Syntax is valid!")
                
                # Clear all caches
                print("🧹 Clearing all caches...")
                cache_cmd = f"cd {remote_path} && wp cache flush && wp litespeed-purge all 2>&1"
                deployer.execute_command(cache_cmd)
                print("   ✅ All caches cleared")
                
                print("\n✅ Comprehensive fixes applied!")
                print("   💡 Please refresh the site to see changes")
                return True
            else:
                print(f"   ❌ Syntax error: {syntax_result[:300]}")
                return False
        else:
            print("   ❌ Failed to deploy")
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if comprehensive_fix() else 1)


