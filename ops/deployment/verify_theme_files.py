#!/usr/bin/env python3
"""
Verify Theme Files Are Ready for Activation
Checks that all required theme files exist in the repository

Usage:
    python ops/deployment/verify_theme_files.py --site houstonsipqueen.com
    python ops/deployment/verify_theme_files.py --all
"""

import argparse
import sys
from pathlib import Path

def verify_theme_files(site_domain, theme_name):
    """
    Verify all required theme files exist
    
    Returns:
        tuple: (all_exist: bool, missing_files: list, existing_files: list)
    """
    repo_root = Path(__file__).parent.parent.parent
    theme_path = repo_root / 'websites' / site_domain / 'wp' / 'wp-content' / 'themes' / theme_name
    
    if not theme_path.exists():
        return False, [], [f"Theme directory not found: {theme_path}"]
    
    # Required files for a WordPress theme
    required_files = {
        'style.css': 'Theme stylesheet with header',
        'functions.php': 'Theme functions and setup',
        'index.php': 'Main template file',
        'header.php': 'Header template',
        'footer.php': 'Footer template',
    }
    
    # Optional but recommended files
    optional_files = {
        'js/main.js': 'JavaScript for interactions',
        'page-quote.php': 'Quote page template (houstonsipqueen only)',
    }
    
    missing = []
    existing = []
    
    # Check required files
    for file_path, description in required_files.items():
        full_path = theme_path / file_path
        if full_path.exists():
            existing.append(f"✅ {file_path} - {description}")
        else:
            missing.append(f"❌ {file_path} - {description}")
    
    # Check optional files
    for file_path, description in optional_files.items():
        full_path = theme_path / file_path
        if full_path.exists():
            existing.append(f"✅ {file_path} - {description} (optional)")
        else:
            existing.append(f"⚠️  {file_path} - {description} (optional, not found)")
    
    all_exist = len(missing) == 0
    
    return all_exist, missing, existing

def main():
    parser = argparse.ArgumentParser(
        description='Verify theme files are ready for activation'
    )
    parser.add_argument(
        '--site',
        type=str,
        help='Site domain (e.g., houstonsipqueen.com)'
    )
    parser.add_argument(
        '--all',
        action='store_true',
        help='Verify all sites that need theme activation'
    )
    
    args = parser.parse_args()
    
    sites_to_check = {
        'houstonsipqueen.com': 'houstonsipqueen',
        'digitaldreamscape.site': 'digitaldreamscape'
    }
    
    if args.all:
        print("=" * 70)
        print("THEME FILE VERIFICATION")
        print("=" * 70)
        
        all_ready = True
        for site, theme in sites_to_check.items():
            print(f"\n📦 {site}")
            print(f"   Theme: {theme}")
            print("-" * 70)
            
            all_exist, missing, existing = verify_theme_files(site, theme)
            
            if all_exist:
                print("✅ All required files present!")
            else:
                print("❌ Missing required files:")
                all_ready = False
            
            for item in existing:
                print(f"   {item}")
            
            if missing:
                print("\n   Missing files:")
                for item in missing:
                    print(f"   {item}")
        
        print("\n" + "=" * 70)
        if all_ready:
            print("✅ All themes are ready for activation!")
        else:
            print("⚠️  Some themes are missing files. Please check above.")
        print("=" * 70)
        
    elif args.site:
        theme = sites_to_check.get(args.site)
        if not theme:
            print(f"❌ Unknown site: {args.site}")
            print(f"   Available sites: {', '.join(sites_to_check.keys())}")
            return
        
        print(f"Verifying theme files for {args.site}...")
        print(f"Theme: {theme}\n")
        
        all_exist, missing, existing = verify_theme_files(args.site, theme)
        
        print("File Status:")
        for item in existing:
            print(f"  {item}")
        
        if missing:
            print("\nMissing Required Files:")
            for item in missing:
                print(f"  {item}")
            print("\n❌ Theme is NOT ready for activation")
        else:
            print("\n✅ Theme is ready for activation!")
    
    else:
        parser.print_help()

if __name__ == '__main__':
    main()

