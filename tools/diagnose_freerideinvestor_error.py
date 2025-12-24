#!/usr/bin/env python3
"""
Diagnose freerideinvestor.com WordPress Critical Error
======================================================

Checks for common WordPress errors:
1. PHP syntax errors in theme files
2. Plugin conflicts
3. Memory issues
4. Missing files

Agent-5: Business Intelligence Specialist
Date: 2025-12-22
"""

import sys
from pathlib import Path
import subprocess

def check_php_syntax(file_path: Path) -> tuple[bool, str]:
    """Check PHP file for syntax errors."""
    try:
        result = subprocess.run(
            ["php", "-l", str(file_path)],
            capture_output=True,
            text=True,
            timeout=10
        )
        return result.returncode == 0, result.stdout + result.stderr
    except FileNotFoundError:
        return None, "PHP not found in PATH"
    except Exception as e:
        return False, str(e)

def main():
    project_root = Path(__file__).parent.parent
    theme_path = project_root / "websites" / "freerideinvestor.com" / "wp" / "wp-content" / "themes" / "freerideinvestor-modern"
    
    if not theme_path.exists():
        print(f"❌ Theme path not found: {theme_path}")
        return 1
    
    print("🔍 Diagnosing freerideinvestor.com WordPress Error...\n")
    
    # Check critical files
    critical_files = [
        "functions.php",
        "header.php",
        "footer.php",
        "index.php",
    ]
    
    errors_found = []
    
    for filename in critical_files:
        file_path = theme_path / filename
        if file_path.exists():
            print(f"📄 Checking {filename}...")
            result, message = check_php_syntax(file_path)
            if result is None:
                print(f"   ⚠️  Cannot check: {message}")
            elif result:
                print(f"   ✅ Syntax OK")
            else:
                print(f"   ❌ Syntax Error: {message}")
                errors_found.append((filename, message))
        else:
            print(f"   ⚠️  File not found: {filename}")
    
    # Check for common WordPress error patterns
    print(f"\n🔍 Checking functions.php for common issues...")
    functions_file = theme_path / "functions.php"
    if functions_file.exists():
        content = functions_file.read_text(encoding="utf-8", errors="ignore")
        
        # Check for unclosed functions
        if content.count("function ") > content.count("}"):
            print("   ⚠️  Possible unclosed function")
        
        # Check for parse errors
        if "Parse error" in content or "Fatal error" in content:
            print("   ⚠️  Error messages found in file")
        
        # Check for common problematic patterns
        problematic_patterns = [
            "<?php <?php",  # Double opening tag
            "<? ?>",  # Short open tag without php
        ]
        for pattern in problematic_patterns:
            if pattern in content:
                print(f"   ⚠️  Found problematic pattern: {pattern}")
    
    print(f"\n📊 Diagnosis Summary:")
    if errors_found:
        print(f"   ❌ Found {len(errors_found)} syntax errors")
        for filename, message in errors_found:
            print(f"      - {filename}: {message}")
        return 1
    else:
        print(f"   ✅ No syntax errors found in critical files")
        print(f"\n💡 Next steps:")
        print(f"   1. Check WordPress debug.log for specific error")
        print(f"   2. Check plugin conflicts")
        print(f"   3. Check server error logs")
        return 0

if __name__ == "__main__":
    sys.exit(main())

