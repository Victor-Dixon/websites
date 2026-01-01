#!/usr/bin/env python3
"""Check theme files to see why templates aren't rendering."""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    deployer.connect()
    
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    theme_path = f"{remote_path}/wp-content/themes/{theme_name}"
    
    # Check header.php
    header_php = f"{theme_path}/header.php"
    header_exists = deployer.execute_command(f"test -f {header_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    if "EXISTS" in header_exists:
        header_content = deployer.execute_command(f"cat {header_php} | head -30")
        print("=" * 70)
        print("HEADER.PHP (first 30 lines)")
        print("=" * 70)
        print(header_content)
        print(f"Length: {len(header_content)} bytes")
        print(f"Has wp_head: {'wp_head' in header_content}")
        print(f"Has body tag: {'<body' in header_content}")
    else:
        print("❌ header.php does not exist!")
    
    print()
    
    # Check footer.php
    footer_php = f"{theme_path}/footer.php"
    footer_exists = deployer.execute_command(f"test -f {footer_php} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    if "EXISTS" in footer_exists:
        footer_content = deployer.execute_command(f"cat {footer_php} | head -30")
        print("=" * 70)
        print("FOOTER.PHP (first 30 lines)")
        print("=" * 70)
        print(footer_content)
        print(f"Length: {len(footer_content)} bytes")
        print(f"Has wp_footer: {'wp_footer' in footer_content}")
    else:
        print("❌ footer.php does not exist!")
    
    print()
    
    # Check if style.css exists (required for theme)
    style_css = f"{theme_path}/style.css"
    style_exists = deployer.execute_command(f"test -f {style_css} && echo 'EXISTS' || echo 'NOT_EXISTS'")
    print(f"style.css exists: {style_exists.strip()}")
    
    # Check functions.php for theme setup
    functions_php = f"{theme_path}/functions.php"
    functions_content = deployer.execute_command(f"cat {functions_php} | head -50")
    print()
    print("=" * 70)
    print("FUNCTIONS.PHP (first 50 lines)")
    print("=" * 70)
    print(functions_content[:1000])
    print(f"Has after_setup_theme: {'after_setup_theme' in functions_content}")
    print(f"Has add_theme_support: {'add_theme_support' in functions_content}")
    
    deployer.disconnect()


if __name__ == "__main__":
    main()


