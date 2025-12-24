#!/usr/bin/env python3
"""
Fix Font Rendering Issues on southwestsecret.com
=================================================

Fixes character spacing and font loading issues causing text rendering problems.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_font_rendering():
    """Fix font rendering issues."""
    print("=" * 70)
    print("🔧 FIXING FONT RENDERING: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        functions_file = f"{remote_path}/wp-content/themes/southwestsecret/functions.php"
        
        print("📖 Reading functions.php...")
        read_cmd = f"cat {functions_file}"
        functions_content = deployer.execute_command(read_cmd)
        
        if not functions_content:
            print("❌ Could not read functions.php")
            return False
        
        # Check if font fixes already exist
        if 'fix_font_rendering' in functions_content or 'font-display: swap' in functions_content:
            print("⚠️  Font fixes may already exist")
            if 'font-display: swap' in functions_content:
                print("   ✅ Font display swap already configured")
                return True
        
        # Add comprehensive font rendering fixes
        font_fixes = '''
/**
 * Fix Font Rendering Issues - Added by Agent-7
 * Ensures proper character spacing and font loading
 */

// Enqueue proper fonts with font-display swap
function southwestsecret_fix_fonts() {
    // Remove any problematic font enqueues
    wp_dequeue_style('google-fonts');
    wp_deregister_style('google-fonts');
    
    // Add proper system fonts with fallbacks
    $font_css = "
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'kern' 1;
            font-kerning: normal;
        }
        
        body, input, textarea, select, button {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
        }
        
        /* Fix character spacing issues */
        h1, h2, h3, h4, h5, h6, p, span, a, li {
            letter-spacing: 0 !important;
            word-spacing: 0.05em !important;
        }
        
        /* Ensure proper encoding */
        html {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
    ";
    
    wp_add_inline_style('wp-block-library', $font_css);
}
add_action('wp_enqueue_scripts', 'southwestsecret_fix_fonts', 999);

// Fix character encoding in head
function southwestsecret_fix_encoding() {
    echo '<meta charset="UTF-8">';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
}
add_action('wp_head', 'southwestsecret_fix_encoding', 1);
'''
        
        # Add to functions.php
        if '?>' in functions_content:
            new_content = functions_content.replace('?>', font_fixes + '\n?>')
        else:
            new_content = functions_content + '\n' + font_fixes
        
        # Save locally
        local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_functions_font_fix.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy
        print("🚀 Deploying font fixes...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print("   ✅ Font fixes deployed")
            
            # Verify syntax
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ Syntax is valid!")
                
                # Clear cache
                print("🧹 Clearing cache...")
                cache_cmd = f"cd {remote_path} && wp cache flush 2>&1"
                deployer.execute_command(cache_cmd)
                print("   ✅ Cache cleared")
                
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
    sys.exit(0 if fix_font_rendering() else 1)


