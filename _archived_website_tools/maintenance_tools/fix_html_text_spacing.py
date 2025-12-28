#!/usr/bin/env python3
"""
Fix HTML Text Spacing Issues
=============================

Fixes actual spaces in HTML source text (not CSS rendering).
Finds and fixes broken domain names and text in template files.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_text_spacing_in_content(content, site_key):
    """Fix common text spacing issues in content."""
    fixes = []
    
    # Domain name fixes
    domain_fixes = {
        "freerideinvestor.com": [
            (r"freerideinve\s+tor\.com", "freerideinvestor.com"),
            (r"freerideinvestor\s+\.com", "freerideinvestor.com"),
        ],
        "crosbyultimateevents.com": [
            (r"cro\s+byultimateevent\s*\.com", "crosbyultimateevents.com"),
            (r"crosby\s+ultimate\s+events\.com", "crosbyultimateevents.com"),
        ],
        "houstonsipqueen.com": [
            (r"hou\s+ton\s+ipqueen\.com", "houstonsipqueen.com"),
            (r"houston\s+sip\s+queen\.com", "houstonsipqueen.com"),
        ],
    }
    
    # Apply fixes for this site
    if site_key in domain_fixes:
        for pattern, replacement in domain_fixes[site_key]:
            new_content = re.sub(pattern, replacement, content, flags=re.IGNORECASE)
            if new_content != content:
                fixes.append(f"Fixed: {pattern} → {replacement}")
                content = new_content
    
    # Common word fixes (if space appears in middle of word)
    # Only fix obvious mistakes like "Con ultation" → "Consultation"
    common_fixes = [
        (r"Con\s+ultation", "Consultation"),
        (r"Ser\s+vice", "Service"),
        (r"Per\s+sonalized", "Personalized"),
        (r"Expe\s+rience", "Experience"),
        (r"Comprehen\s+sive", "Comprehensive"),
        (r"Occa\s+sion", "Occasion"),
        (r"Choo\s+se", "Choose"),
        (r"Cu\s+tom", "Custom"),
        (r"preference\s+s", "preferences"),
        (r"de\s+signed", "designed"),
        (r"spe\s+cifically", "specifically"),
        (r"Exceptional\s+cui\s+ine", "Exceptional cuisine"),
        (r"exe\s+cution", "execution"),
        (r"Experti\s+se", "Expertise"),
        (r"seamle\s+s", "seamless"),
        (r"Meticulou\s+s", "Meticulous"),
        (r"expectation\s+s", "expectations"),
        (r"multi-cour\s+se", "multi-course"),
        (r"cla\s+se", "classes"),
        (r"Re\s+triction", "Restriction"),
        (r"Accommodation", "Accommodation"),  # Check for spacing
        (r"Full-\s+service", "Full-service"),
        (r"coordination", "coordination"),  # Check
        (r"Partie\s+s", "Parties"),
        (r"Celebration", "Celebration"),  # Check
        (r"Package\s+s", "Packages"),
        (r"Available", "Available"),  # Check
        (r"tarted", "started"),  # "Get tarted" → "Get started"
        (r"di\s+cu\s+", "discuss "),
        (r"vi\s+on", "vision"),
        (r"re\s+pond", "respond"),
        (r"hour\s+s", "hours"),
        (r"chedule", "schedule"),
        (r"di\s+cu", "discuss"),
    ]
    
    for pattern, replacement in common_fixes:
        new_content = re.sub(pattern, replacement, content, flags=re.IGNORECASE)
        if new_content != content:
            fixes.append(f"Fixed: {pattern} → {replacement}")
            content = new_content
    
    return content, fixes


def fix_template_file(deployer, remote_path, site_key):
    """Fix text spacing in a template file."""
    print(f"\n📝 Checking {remote_path}...")
    
    exists = deployer.execute_command(f"test -f {remote_path} && echo 'EXISTS' || echo 'MISSING'")
    if "MISSING" in exists:
        return False, []
    
    content = deployer.execute_command(f"cat {remote_path}")
    original_content = content
    
    # Fix text spacing
    fixed_content, fixes = fix_text_spacing_in_content(content, site_key)
    
    if fixes:
        print(f"   Found {len(fixes)} fixes to apply")
        for fix in fixes[:5]:  # Show first 5
            print(f"      {fix}")
        if len(fixes) > 5:
            print(f"      ... and {len(fixes) - 5} more")
        
        # Create backup
        backup_path = f"{remote_path}.backup.text_fix"
        deployer.execute_command(f"cp {remote_path} {backup_path}")
        print(f"   ✅ Backup created: {backup_path}")
        
        # Save locally
        local_file = Path(__file__).parent.parent / "docs" / f"{site_key.replace('.', '_')}_{Path(remote_path).name}_fixed"
        local_file.write_text(fixed_content, encoding='utf-8')
        
        # Deploy
        success = deployer.deploy_file(local_file, remote_path)
        
        if success:
            print(f"   ✅ Fixes applied")
            return True, fixes
        else:
            print(f"   ❌ Failed to deploy")
            return False, fixes
    else:
        print(f"   ✅ No spacing issues found")
        return False, []
    
    return False, []


def fix_site(site_key, theme_name=None):
    """Fix text spacing issues for a site."""
    print("\n" + "=" * 70)
    print(f"FIXING HTML TEXT SPACING: {site_key}")
    print("=" * 70)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_key, site_configs)
    
    if not deployer.connect():
        print(f"❌ Failed to connect to {site_key}")
        return False
    
    try:
        # Detect theme if not provided
        if not theme_name:
            remote_path = f"domains/{site_key}/public_html"
            active_theme = deployer.execute_command(
                f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>/dev/null || "
                f"ls wp-content/themes/ | head -1"
            ).strip()
            theme_name = active_theme.split('\n')[0] if active_theme else "default"
            print(f"📌 Using theme: {theme_name}")
        
        base_path = f"domains/{site_key}/public_html/wp-content/themes/{theme_name}"
        
        # Fix header.php
        header_path = f"{base_path}/header.php"
        header_fixed, header_fixes = fix_template_file(deployer, header_path, site_key)
        
        # Fix footer.php if it exists
        footer_path = f"{base_path}/footer.php"
        footer_fixed, footer_fixes = fix_template_file(deployer, footer_path, site_key)
        
        # Fix index.php/home.php if they exist
        index_path = f"{base_path}/index.php"
        index_fixed, index_fixes = fix_template_file(deployer, index_path, site_key)
        
        total_fixes = len(header_fixes) + len(footer_fixes) + len(index_fixes)
        
        if total_fixes > 0:
            print(f"\n✅ Applied {total_fixes} text spacing fixes")
            print(f"   ⏳ Please clear cache and test the site")
            return True
        else:
            print(f"\n⚠️  No text spacing issues found in templates")
            print(f"   (Issue might be in WordPress content/database)")
            return False
            
    except Exception as e:
        print(f"❌ Error fixing {site_key}: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    if len(sys.argv) > 1:
        site_key = sys.argv[1]
        theme_name = sys.argv[2] if len(sys.argv) > 2 else None
        fix_site(site_key, theme_name)
    else:
        # Fix all affected sites
        sites_to_fix = [
            ("freerideinvestor.com", "freerideinvestor-modern"),
            ("crosbyultimateevents.com", None),
            ("houstonsipqueen.com", None),
        ]
        
        print("=" * 70)
        print("FIXING HTML TEXT SPACING ISSUES")
        print("=" * 70)
        
        for site_key, theme_name in sites_to_fix:
            fix_site(site_key, theme_name)
    
    return 0


if __name__ == "__main__":
    sys.exit(main())






