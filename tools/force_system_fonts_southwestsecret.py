#!/usr/bin/env python3
"""
Force System Fonts - Fix Rendering Issues
==========================================

Forces system fonts to fix character rendering problems.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def force_system_fonts():
    """Force system fonts to fix rendering."""
    print("=" * 70)
    print("🔧 FORCING SYSTEM FONTS: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        style_css = f"{remote_path}/wp-content/themes/southwestsecret/style.css"
        
        print("📖 Reading style.css...")
        read_cmd = f"cat {style_css}"
        css_content = deployer.execute_command(read_cmd)
        
        # Add aggressive font override
        font_override = '''
/* FORCE SYSTEM FONTS - Fix Character Rendering - Added by Agent-7 */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

/* Override ALL fonts with system fonts */
* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: optimizeLegibility !important;
    font-feature-settings: 'kern' 1 !important;
    font-kerning: normal !important;
    letter-spacing: 0 !important;
    word-spacing: 0.05em !important;
}

/* Ensure no custom fonts override */
body, html, h1, h2, h3, h4, h5, h6, p, span, a, li, div, nav, section, article, header, footer {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    font-display: swap !important;
}

/* Fix character spacing */
* {
    text-transform: none !important;
    font-variant: normal !important;
    font-stretch: normal !important;
}

/* Ensure UTF-8 rendering */
html {
    -webkit-text-size-adjust: 100% !important;
    -ms-text-size-adjust: 100% !important;
    text-size-adjust: 100% !important;
}
'''
        
        # Append to CSS
        new_css = css_content.rstrip() + '\n' + font_override
        
        # Save locally
        local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_style_font_override.css"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_css, encoding='utf-8')
        
        # Deploy
        print("🚀 Deploying font override...")
        success = deployer.deploy_file(local_file, style_css)
        
        if success:
            print("   ✅ Font override deployed")
            
            # Clear cache
            print("🧹 Clearing cache...")
            cache_cmd = f"cd {remote_path} && wp cache flush && wp litespeed-purge all 2>&1"
            deployer.execute_command(cache_cmd)
            print("   ✅ Cache cleared")
            
            print("\n✅ System fonts forced!")
            print("   💡 Please hard refresh (Ctrl+F5) to see changes")
            return True
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
    sys.exit(0 if force_system_fonts() else 1)


