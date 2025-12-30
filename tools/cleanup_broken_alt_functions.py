#!/usr/bin/env python3
"""
Cleanup Broken Alt Text Functions
==================================

Removes broken alt text functions from functions.php files.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


ALL_SITES = [
    "ariajet.site",
    "crosbyultimateevents.com",
    "dadudekc.com",
    "digitaldreamscape.site",
    "freerideinvestor.com",
    "houstonsipqueen.com",
    "prismblossom.online",
    "southwestsecret.com",
    "tradingrobotplug.com",
    "weareswarm.online",
    "weareswarm.site",
]

def _normalize_php_file(content: str) -> str:
    """
    Normalize a PHP file string to avoid accidental output and tag issues.

    - Strip UTF-8 BOM
    - Normalize newlines to LF
    - Ensure file begins at first '<?php' (removes accidental leading output)
    - Remove any mid-file '?> ... <?php' transitions (not needed in functions.php)
    - Remove trailing '?>' (best practice)
    """
    if not content:
        return content

    # Normalize newlines early to keep regex stable.
    content = content.replace("\r\n", "\n").replace("\r", "\n")

    # Strip BOM if present.
    content = content.lstrip("\ufeff")

    # If there's anything before the first PHP open tag, drop it.
    first_php = content.find("<?php")
    if first_php > 0:
        content = content[first_php:]

    # Remove close/open tag transitions (avoid accidental output segments).
    content = re.sub(r"\?>\s*<\?php\s*", "\n", content, flags=re.IGNORECASE)

    # Remove a trailing closing tag (common best practice for WP).
    content = re.sub(r"\?>\s*\Z", "", content, flags=re.IGNORECASE)

    return content


def _remove_alt_text_snippets(content: str) -> tuple[str, bool]:
    """
    Remove known alt-text snippet blocks safely.

    Important: avoid broad regexes that can cut through regex literals inside PHP,
    which previously caused corrupted outputs like stray '?>/' fragments.
    """
    if not content:
        return content, False

    original = content

    # Prefer removing the whole "Agent-7" block as a single unit.
    agent7_block = re.compile(
        r"""
        /\*\*                                      # opening docblock
        [\s\S]*?
        Add\s+Missing\s+Alt\s+Text\s+to\s+Images    # title
        \s*-\s*Added\s+by\s+Agent-7                 # signature
        [\s\S]*?
        add_filter\(\s*['"]the_excerpt['"]\s*,\s*['"]add_missing_alt_text_to_widgets['"]\s*,\s*20\s*\)\s*;
        \s*
        """,
        flags=re.IGNORECASE | re.VERBOSE,
    )
    content = agent7_block.sub("", content)

    # Other known legacy variants (remove function + its specific hook line).
    # These are anchored to the exact hook registration line to avoid over-matching.
    legacy_blocks = [
        re.compile(
            r"(?is)function\s+add_alt_to_content_images\s*\([^)]*\)\s*\{[\s\S]*?\}\s*add_filter\(\s*['\"]the_content['\"]\s*,\s*['\"]add_alt_to_content_images['\"]\s*,[\s\S]*?\);\s*"
        ),
        re.compile(
            r"(?is)function\s+add_missing_alt_text_to_content\s*\([^)]*\)\s*\{[\s\S]*?\}\s*add_filter\(\s*['\"]the_content['\"]\s*,\s*['\"]add_missing_alt_text_to_content['\"]\s*,[\s\S]*?\);\s*"
        ),
        re.compile(
            r"(?is)function\s+add_missing_alt_text_to_thumbnails\s*\([^)]*\)\s*\{[\s\S]*?\}\s*add_filter\(\s*['\"]post_thumbnail_html['\"]\s*,\s*['\"]add_missing_alt_text_to_thumbnails['\"]\s*,[\s\S]*?\);\s*"
        ),
        re.compile(
            r"(?is)function\s+add_missing_alt_text_to_widgets\s*\([^)]*\)\s*\{[\s\S]*?\}\s*add_filter\(\s*['\"]widget_text['\"]\s*,\s*['\"]add_missing_alt_text_to_widgets['\"]\s*,[\s\S]*?\);\s*add_filter\(\s*['\"]the_excerpt['\"]\s*,\s*['\"]add_missing_alt_text_to_widgets['\"]\s*,[\s\S]*?\);\s*"
        ),
    ]
    for pat in legacy_blocks:
        content = pat.sub("", content)

    removed = content != original
    return content, removed


def cleanup_broken_functions(site_name: str):
    """Remove broken alt text functions."""
    print(f"\n{'='*70}")
    print(f"🧹 CLEANUP: {site_name}")
    print(f"{'='*70}")
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed: {e}")
        return False
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        theme_path = f"{remote_path}/wp-content/themes"
        
        # Find theme
        site_theme_name = site_name.replace('.', '').replace('-', '')
        possible_themes = [site_theme_name, site_name.split('.')[0], 'default', 'twentytwentyfour']
        
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        if themes_list:
            for line in themes_list.strip().split('\n'):
                if line.strip() and line.strip() not in possible_themes:
                    possible_themes.append(line.strip())
        
        functions_file = None
        for theme_name in possible_themes:
            functions_path = f"{theme_path}/{theme_name}/functions.php"
            check_cmd = f"test -f {functions_path} && echo 'exists' || echo 'not found'"
            if 'exists' in deployer.execute_command(check_cmd):
                functions_file = functions_path
                break
        
        if not functions_file:
            print("   ⚠️  Theme not found")
            return False
        
        # Read file
        read_cmd = f"cat {functions_file}"
        content = deployer.execute_command(read_cmd)
        
        if not content:
            return False
        
        # Remove broken alt text functions (safe, block-based removal).
        cleaned, removed = _remove_alt_text_snippets(content)
        
        if not removed:
            print("   ✅ No broken functions found")
            return True

        # Normalize tags/newlines and avoid accidental output.
        cleaned = _normalize_php_file(cleaned)
        
        # Clean up extra blank lines
        cleaned = re.sub(r'\n{3,}', '\n\n', cleaned)
        
        # Save and deploy
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_cleaned.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(cleaned, encoding='utf-8')
        
        print(f"   🚀 Deploying cleaned functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            # Verify syntax
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print(f"   ✅ Cleaned and syntax is valid!")
                return True
            else:
                print(f"   ⚠️  Still has syntax errors")
                return False
        else:
            return False
            
    except Exception as e:
        print(f"❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    print("=" * 70)
    print("🧹 CLEANUP BROKEN ALT TEXT FUNCTIONS")
    print("=" * 70)
    print()
    
    for site_name in ALL_SITES:
        cleanup_broken_functions(site_name)
    
    print("\n✅ Cleanup complete!")
    print("💡 Next: Run add_alt_text_simple.py again")


if __name__ == "__main__":
    sys.exit(main())

