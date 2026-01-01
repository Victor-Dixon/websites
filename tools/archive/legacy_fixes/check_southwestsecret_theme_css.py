#!/usr/bin/env python3
"""
Check and Fix Theme CSS for Font Issues
========================================

Checks the theme's style.css for font rendering problems.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_and_fix_css():
    """Check and fix CSS font issues."""
    print("=" * 70)
    print("🔍 CHECKING THEME CSS: southwestsecret")
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
        
        if not css_content:
            print("   ⚠️  style.css not found or empty")
            return False
        
        print(f"   File size: {len(css_content)} bytes")
        
        # Check for font issues
        issues_found = []
        
        if 'letter-spacing' in css_content:
            # Check for negative or problematic letter-spacing
            import re
            letter_spacing_matches = re.findall(r'letter-spacing:\s*([^;]+)', css_content)
            for match in letter_spacing_matches:
                if 'em' in match or 'px' in match:
                    try:
                        value = float(match.replace('em', '').replace('px', '').strip())
                        if value < -0.1 or value > 0.2:
                            issues_found.append(f"Problematic letter-spacing: {match}")
                    except:
                        pass
        
        if 'font-family' in css_content:
            # Check if using problematic fonts
            if 'font-family' in css_content and 'Inter' not in css_content and 'system' not in css_content.lower():
                issues_found.append("May be using custom fonts without proper fallbacks")
        
        if issues_found:
            print("   ⚠️  Potential issues found:")
            for issue in issues_found:
                print(f"      - {issue}")
        else:
            print("   ✅ No obvious font issues in CSS")
        
        # Add font fixes to CSS if needed
        if 'font-smoothing' not in css_content or 'text-rendering' not in css_content:
            print("\n🔧 Adding font rendering fixes to CSS...")
            
            font_fixes = '''
/* Font Rendering Fixes - Added by Agent-7 */
* {
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: optimizeLegibility !important;
    font-feature-settings: 'kern' 1 !important;
    font-kerning: normal !important;
}

body, input, textarea, select, button {
    letter-spacing: normal !important;
    word-spacing: normal !important;
}

h1, h2, h3, h4, h5, h6, p, span, a, li, div {
    letter-spacing: 0 !important;
    word-spacing: 0.05em !important;
}
'''
            
            # Append to CSS
            new_css = css_content.rstrip() + '\n' + font_fixes
            
            # Save locally
            local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_style_fixed.css"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(new_css, encoding='utf-8')
            
            # Deploy
            print("   🚀 Deploying fixed style.css...")
            success = deployer.deploy_file(local_file, style_css)
            
            if success:
                print("   ✅ CSS fixes deployed")
                return True
            else:
                print("   ❌ Failed to deploy")
                return False
        else:
            print("   ✅ Font rendering already configured")
            return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if check_and_fix_css() else 1)


