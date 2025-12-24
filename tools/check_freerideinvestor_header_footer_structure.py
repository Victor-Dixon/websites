#!/usr/bin/env python3
"""
Check freerideinvestor.com Header/Footer Structure
=================================================

Checks header.php and footer.php for structural issues that might prevent
main content from rendering.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_header_structure(deployer):
    """Check header.php structure."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    header_php = f"{remote_path}/wp-content/themes/{theme_name}/header.php"
    
    print("=" * 70)
    print("HEADER.PHP STRUCTURE ANALYSIS")
    print("=" * 70)
    
    content = deployer.execute_command(f"cat {header_php}")
    
    # Check for main tag opening
    has_main_open = "<main" in content or "<div class=\"main" in content or "<div id=\"main" in content
    has_main_close = "</main>" in content
    
    # Check for site-wrapper or container
    has_site_wrapper = "site-wrapper" in content or "site-container" in content
    
    # Check where header ends
    lines = content.split('\n')
    print(f"Total lines: {len(lines)}")
    print(f"Has <main opening: {has_main_open}")
    print(f"Has </main closing: {has_main_close}")
    print(f"Has site-wrapper: {has_site_wrapper}")
    
    # Find where header closes (look for closing divs or end of header)
    print("\nHeader structure:")
    in_header = False
    for i, line in enumerate(lines[:50], 1):  # First 50 lines
        if "<header" in line or "site-header" in line:
            in_header = True
        if "</header>" in line:
            in_header = False
        if i <= 30 or in_header:
            print(f"{i:3d}: {line[:80]}")
    
    return {
        "has_main_open": has_main_open,
        "has_main_close": has_main_close,
        "has_site_wrapper": has_site_wrapper,
        "content_length": len(content)
    }


def check_footer_structure(deployer):
    """Check footer.php structure."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    footer_php = f"{remote_path}/wp-content/themes/{theme_name}/footer.php"
    
    print("\n" + "=" * 70)
    print("FOOTER.PHP STRUCTURE ANALYSIS")
    print("=" * 70)
    
    content = deployer.execute_command(f"cat {footer_php}")
    
    # Check if footer starts with closing main
    starts_with_main_close = content.strip().startswith("</main>")
    
    # Check for main closing
    has_main_close = "</main>" in content
    
    # Check for site-wrapper closing
    has_site_wrapper_close = "</div>" in content and ("site-wrapper" in content or "site-container" in content)
    
    lines = content.split('\n')
    print(f"Total lines: {len(lines)}")
    print(f"Starts with </main>: {starts_with_main_close}")
    print(f"Has </main closing: {has_main_close}")
    print(f"Has site-wrapper closing: {has_site_wrapper_close}")
    
    # Show first 20 lines
    print("\nFooter structure (first 20 lines):")
    for i, line in enumerate(lines[:20], 1):
        print(f"{i:3d}: {line[:80]}")
    
    return {
        "starts_with_main_close": starts_with_main_close,
        "has_main_close": has_main_close,
        "has_site_wrapper_close": has_site_wrapper_close,
        "content_length": len(content)
    }


def check_template_hierarchy(deployer):
    """Check which template WordPress is using."""
    remote_path = "domains/freerideinvestor.com/public_html"
    
    print("\n" + "=" * 70)
    print("WORDPRESS TEMPLATE HIERARCHY CHECK")
    print("=" * 70)
    
    # Check homepage setting
    show_on_front = deployer.execute_command(
        f"cd {remote_path} && wp option get show_on_front --allow-root 2>/dev/null || echo 'posts'"
    ).strip()
    
    print(f"Show on front: {show_on_front}")
    
    if show_on_front == "posts":
        print("\nTemplate hierarchy for blog posts homepage:")
        print("  1. front-page.php (if exists)")
        print("  2. home.php (if exists)")
        print("  3. index.php (fallback)")
    else:
        print("\nTemplate hierarchy for static page homepage:")
        print("  1. front-page.php (if exists)")
        print("  2. page-{slug}.php (if exists)")
        print("  3. page-{id}.php (if exists)")
        print("  4. page.php (if exists)")
        print("  5. singular.php (if exists)")
        print("  6. index.php (fallback)")
    
    # Check which templates exist
    theme_name = "freerideinvestor-modern"
    templates = ["front-page.php", "home.php", "index.php", "page.php"]
    
    print("\nTemplate files status:")
    for template in templates:
        template_path = f"{remote_path}/wp-content/themes/{theme_name}/{template}"
        exists = deployer.execute_command(f"test -f {template_path} && echo 'EXISTS' || echo 'MISSING'")
        status = "✅" if "EXISTS" in exists else "❌"
        print(f"  {status} {template}")
        
        if "EXISTS" in exists:
            # Check if it has WordPress loop
            content = deployer.execute_command(f"cat {template_path}")
            has_loop = "have_posts" in content or "the_post" in content
            has_main = "<main" in content
            print(f"      - Has loop: {has_loop}")
            print(f"      - Has <main>: {has_main}")


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        header_info = check_header_structure(deployer)
        footer_info = check_footer_structure(deployer)
        check_template_hierarchy(deployer)
        
        print("\n" + "=" * 70)
        print("STRUCTURAL ANALYSIS SUMMARY")
        print("=" * 70)
        
        # Check for conflicts
        if footer_info["starts_with_main_close"] and not header_info["has_main_open"]:
            print("⚠️  CONFLICT: Footer closes <main> but header doesn't open it")
            print("   This suggests main should be opened in template files")
        
        if header_info["has_main_open"] and footer_info["has_main_close"]:
            print("⚠️  CONFLICT: Both header and footer manage <main> tag")
            print("   Template files should NOT open <main> if header/footer do")
        
        if not header_info["has_main_open"] and not footer_info["starts_with_main_close"]:
            print("✅ Header doesn't open <main>, footer doesn't close it")
            print("   Template files should open/close <main>")
        
        if footer_info["starts_with_main_close"]:
            print("⚠️  Footer starts with </main>")
            print("   This means <main> should be opened BEFORE footer is called")
            print("   Check if template files are opening <main> correctly")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()
    
    return 0


if __name__ == "__main__":
    sys.exit(main())


