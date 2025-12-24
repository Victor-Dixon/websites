#!/usr/bin/env python3
"""
Enhanced PHP Syntax Checker
============================

Standalone tool for enhanced PHP syntax checking with line numbers,
context, common pattern detection, and fix suggestions.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path
from typing import Dict, List, Optional, Tuple

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def detect_common_patterns(content: str, line_number: int) -> List[str]:
    """Detect common PHP syntax error patterns."""
    suggestions = []
    lines = content.split('\n')
    
    if line_number > 0 and line_number <= len(lines):
        line = lines[line_number - 1]
        
        # Check for hyphens in identifiers
        if re.search(r'\$[a-zA-Z_][a-zA-Z0-9_-]*-[a-zA-Z0-9_-]*', line):
            suggestions.append("Hyphen in variable name - PHP identifiers cannot contain hyphens, use underscores")
        
        if re.search(r'function\s+[a-zA-Z_][a-zA-Z0-9_-]*-[a-zA-Z0-9_-]*\s*\(', line):
            suggestions.append("Hyphen in function name - PHP function names cannot contain hyphens, use underscores")
        
        # Check for unclosed brackets
        open_braces = line.count('{')
        close_braces = line.count('}')
        open_parens = line.count('(')
        close_parens = line.count(')')
        open_square = line.count('[')
        close_square = line.count(']')
        
        if open_braces > close_braces:
            suggestions.append("Possible unclosed brace '{' - check for matching closing brace")
        if open_parens > close_parens:
            suggestions.append("Possible unclosed parenthesis '(' - check for matching closing parenthesis")
        if open_square > close_square:
            suggestions.append("Possible unclosed square bracket '[' - check for matching closing bracket")
        
        # Check for missing semicolon
        if line.strip() and not line.strip().endswith((';', '{', '}', ':', '?>', '*/')):
            if '=' in line or 'return' in line or 'echo' in line or 'print' in line:
                suggestions.append("Possible missing semicolon at end of line")
        
        # Check for unterminated strings
        single_quotes = line.count("'") - line.count("\\'")
        double_quotes = line.count('"') - line.count('\\"')
        if single_quotes % 2 != 0:
            suggestions.append("Possible unterminated single-quoted string")
        if double_quotes % 2 != 0:
            suggestions.append("Possible unterminated double-quoted string")
    
    return suggestions


def check_php_syntax_enhanced(deployer, remote_file_path: str) -> Dict:
    """
    Enhanced PHP syntax check with detailed error information.
    
    Uses SimpleWordPressDeployer's check_php_syntax method and adds
    common pattern detection and fix suggestions.
    """
    # Use the deployer's check_php_syntax method
    result = deployer.check_php_syntax(remote_file_path)
    
    # Add common pattern detection if error found
    if not result.get('valid') and result.get('line_number'):
        # Read file content for pattern detection
        content = deployer.execute_command(f"cat {remote_file_path}")
        suggestions = detect_common_patterns(content, result.get('line_number'))
        
        if suggestions:
            result['suggestions'] = suggestions
            result['common_patterns_detected'] = True
        else:
            result['suggestions'] = []
            result['common_patterns_detected'] = False
    
    return result


def check_multiple_files(deployer, file_paths: List[str]) -> Dict[str, Dict]:
    """Check syntax of multiple PHP files."""
    results = {}
    
    for file_path in file_paths:
        print(f"Checking {file_path}...")
        result = check_php_syntax_enhanced(deployer, file_path)
        results[file_path] = result
        
        if result.get('valid'):
            print(f"  ✅ Valid syntax")
        else:
            print(f"  ❌ Syntax error on line {result.get('line_number', 'unknown')}")
            if result.get('suggestions'):
                print(f"  💡 Suggestions:")
                for suggestion in result['suggestions']:
                    print(f"     - {suggestion}")
    
    return results


def main():
    """Main execution."""
    if len(sys.argv) < 2:
        print("Usage: python enhanced_php_syntax_checker.py <site_key> [file_paths...]")
        print("Example: python enhanced_php_syntax_checker.py freerideinvestor.com wp-content/themes/theme/functions.php")
        return 1
    
    site_key = sys.argv[1]
    file_paths = sys.argv[2:] if len(sys.argv) > 2 else []
    
    print("=" * 70)
    print("ENHANCED PHP SYNTAX CHECKER")
    print("=" * 70)
    print(f"Site: {site_key}")
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_key, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_key}/public_html"
        
        if file_paths:
            # Check specific files
            full_paths = [f"{remote_path}/{fp}" if not fp.startswith('/') else fp for fp in file_paths]
            results = check_multiple_files(deployer, full_paths)
        else:
            # Check common theme files
            theme_name = "freerideinvestor-modern" if site_key == "freerideinvestor.com" else "default"
            common_files = [
                f"{remote_path}/wp-content/themes/{theme_name}/functions.php",
                f"{remote_path}/wp-content/themes/{theme_name}/header.php",
                f"{remote_path}/wp-content/themes/{theme_name}/footer.php",
                f"{remote_path}/wp-content/themes/{theme_name}/index.php",
            ]
            
            print("Checking common theme files...")
            results = check_multiple_files(deployer, common_files)
        
        # Summary
        print("\n" + "=" * 70)
        print("SUMMARY")
        print("=" * 70)
        
        valid_count = sum(1 for r in results.values() if r.get('valid'))
        total_count = len(results)
        
        print(f"Files checked: {total_count}")
        print(f"Valid syntax: {valid_count}")
        print(f"Syntax errors: {total_count - valid_count}")
        
        if total_count - valid_count > 0:
            print("\nFiles with errors:")
            for file_path, result in results.items():
                if not result.get('valid'):
                    print(f"  - {file_path}")
                    print(f"    Line {result.get('line_number', 'unknown')}: {result.get('error_message', 'Unknown error')}")
                    if result.get('suggestions'):
                        for suggestion in result['suggestions']:
                            print(f"    💡 {suggestion}")
        
        return 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())

