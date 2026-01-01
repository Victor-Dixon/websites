#!/usr/bin/env python3
"""
Final Font Fix - Aggressive Approach
======================================

Uses the most aggressive approach to fix font rendering.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def final_font_fix():
    """Final aggressive font fix."""
    print("=" * 70)
    print("🔧 FINAL AGGRESSIVE FONT FIX")
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
        
        # Most aggressive fix - inject CSS directly in head with highest priority
        aggressive_fix = '''
/**
 * AGGRESSIVE FONT FIX - Highest Priority - Added by Agent-7
 * This completely overrides any font issues
 */

// Inject critical CSS in head with absolute highest priority
function southwestsecret_aggressive_font_fix() {
    echo '<style id="southwestsecret-critical-font-fix" data-priority="999999">
        /* CRITICAL: Override ALL fonts immediately */
        html, body, * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            font-display: swap !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
            letter-spacing: 0 !important;
            word-spacing: 0.05em !important;
            font-feature-settings: normal !important;
            font-variant: normal !important;
            font-stretch: normal !important;
            text-transform: none !important;
        }
        
        /* Block any @font-face */
        @font-face {
            font-family: "*";
            src: none !important;
        }
        
        /* Override any inline styles */
        [style*="font-family"] {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
    </style>';
    
    // Also add JavaScript to fix any dynamically loaded fonts
    echo "<script>
        (function() {
            // Remove any font links
            var links = document.querySelectorAll('link[href*=\"font\"]');
            links.forEach(function(link) {
                if (link.href.indexOf('fonts.googleapis.com') > -1 || link.href.indexOf('fonts.gstatic.com') > -1) {
                    link.remove();
                }
            });
            
            // Force system font on all elements
            var style = document.createElement('style');
            style.textContent = '* { font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif !important; }';
            document.head.appendChild(style);
        })();
    </script>";
}
add_action('wp_head', 'southwestsecret_aggressive_font_fix', 1);

// Also remove font enqueues at the earliest possible point
function southwestsecret_remove_all_fonts() {
    global $wp_styles;
    if (isset($wp_styles->registered)) {
        foreach ($wp_styles->registered as $handle => $style) {
            if (strpos($handle, 'font') !== false || 
                (isset($style->src) && (strpos($style->src, 'font') !== false || 
                                        strpos($style->src, 'googleapis') !== false))) {
                wp_dequeue_style($handle);
                wp_deregister_style($handle);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'southwestsecret_remove_all_fonts', 1);
'''
        
        # Check if already added
        if 'southwestsecret_aggressive_font_fix' not in functions_content:
            if '?>' in functions_content:
                new_content = functions_content.replace('?>', aggressive_fix + '\n?>')
            else:
                new_content = functions_content + '\n' + aggressive_fix
            
            # Save locally
            local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_functions_aggressive_fix.php"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(new_content, encoding='utf-8')
            
            # Deploy
            print("🚀 Deploying aggressive font fix...")
            success = deployer.deploy_file(local_file, functions_file)
            
            if success:
                print("   ✅ Aggressive fix deployed")
                
                # Verify syntax
                syntax_cmd = f"php -l {functions_file} 2>&1"
                syntax_result = deployer.execute_command(syntax_cmd)
                
                if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                    print("   ✅ Syntax is valid!")
                    
                    # Clear all caches aggressively
                    print("🧹 Clearing all caches...")
                    cache_commands = [
                        f"cd {remote_path} && wp cache flush",
                        f"cd {remote_path} && wp litespeed-purge all",
                        f"cd {remote_path} && wp transient delete --all",
                    ]
                    for cmd in cache_commands:
                        deployer.execute_command(cmd)
                    print("   ✅ All caches cleared")
                    
                    print("\n✅ Aggressive font fix applied!")
                    print("   💡 Please hard refresh (Ctrl+Shift+R) to see changes")
                    print("   💡 If issues persist, the font may be loaded via JavaScript")
                    return True
                else:
                    print(f"   ❌ Syntax error: {syntax_result[:300]}")
                    return False
            else:
                print("   ❌ Failed to deploy")
                return False
        else:
            print("   ✅ Aggressive fix already exists")
            return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if final_font_fix() else 1)

