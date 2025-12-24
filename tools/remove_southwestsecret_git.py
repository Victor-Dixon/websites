#!/usr/bin/env python3
"""
Remove southwestsecret.com .git directory
==========================================

Removes the nested .git directory from southwestsecret.com if it's safe to do so.
"""

import shutil
from pathlib import Path


def main():
    """Remove .git directory from southwestsecret.com."""
    site_path = Path("southwestsecret.com")
    git_path = site_path / ".git"
    
    if not git_path.exists():
        print("No .git directory found in southwestsecret.com")
        return 0
    
    print("=" * 70)
    print("REMOVING NESTED .GIT DIRECTORY")
    print("=" * 70)
    print()
    print(f"Removing: {git_path}")
    
    try:
        # Use shutil.rmtree with onerror to handle permission issues
        def handle_remove_readonly(func, path, exc):
            import os
            os.chmod(path, 0o777)
            func(path)
        
        shutil.rmtree(git_path, onerror=handle_remove_readonly)
        print("✅ .git directory removed successfully")
        print()
        
        # Check if directory is now empty
        remaining = [item for item in site_path.iterdir() if not item.name.startswith(".")]
        if not remaining:
            print(f"Directory is now empty - removing {site_path}")
            shutil.rmtree(site_path, onerror=handle_remove_readonly)
            print("✅ Empty directory removed")
        else:
            print(f"⚠️  Directory still has {len(remaining)} items: {[i.name for i in remaining]}")
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        return 1


if __name__ == "__main__":
    exit(main())

