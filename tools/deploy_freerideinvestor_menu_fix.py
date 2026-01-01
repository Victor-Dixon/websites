#!/usr/bin/env python3
"""
Deploy freerideinvestor.com Menu Navigation Fix
===============================================

Deploys menu navigation fixes to freerideinvestor.com WordPress theme.

Agent-7: Web Development Specialist
Task: Deploy menu navigation fixes
"""

import json
from pathlib import Path
from datetime import datetime
import sys

def get_site_info():
    """Get freerideinvestor.com site information."""
    project_root = Path(__file__).parent.parent
    registry_file = project_root / "configs" / "sites_registry.json"
    
    with open(registry_file, 'r', encoding='utf-8') as f:
        registry = json.load(f)
    
    return registry.get("freerideinvestor.com", {})

def read_fix_file(filename):
    """Read fix file content."""
    project_root = Path(__file__).parent.parent
    fix_file = project_root / "docs" / "freerideinvestor" / filename
    
    if not fix_file.exists():
        print(f"❌ Fix file not found: {fix_file}")
        return None
    
    with open(fix_file, 'r', encoding='utf-8') as f:
        return f.read()

def create_deployment_script():
    """Create deployment script for menu fixes."""
    php_fix = read_fix_file("freerideinvestor_menu_navigation_fix.php")
    if not php_fix:
        return None
    
    # Extract just the PHP code (remove markdown if present)
    php_code = php_fix
    if "```php" in php_code:
        php_code = php_code.split("```php")[1].split("```")[0].strip()
    elif "```" in php_code:
        php_code = php_code.split("```")[1].split("```")[0].strip()
    
    deployment_script = f"""#!/usr/bin/env python3
\"\"\"
Deploy Menu Navigation Fix to freerideinvestor.com
===================================================

This script adds menu navigation fixes to the WordPress theme's functions.php.

Usage:
    python deploy_menu_fix_to_freerideinvestor.py

Note: This requires SFTP/SSH access to the WordPress installation.
\"\"\"

import sys
from pathlib import Path

# Menu navigation fix PHP code
MENU_FIX_PHP = '''
{php_code}
'''

def main():
    print("=" * 70)
    print("DEPLOY MENU NAVIGATION FIX TO FREERIDEINVESTOR.COM")
    print("=" * 70)
    print()
    print("This fix adds:")
    print("  - Menu toggle CSS styles")
    print("  - Menu toggle JavaScript functionality")
    print("  - Responsive menu behavior")
    print()
    print("To deploy:")
    print("1. Access WordPress theme's functions.php file")
    print("2. Add the following code to the end of functions.php:")
    print()
    print("-" * 70)
    print(MENU_FIX_PHP)
    print("-" * 70)
    print()
    print("3. Save functions.php")
    print("4. Clear WordPress cache")
    print("5. Test menu navigation")
    print()
    print("✅ Fix code ready for deployment")

if __name__ == "__main__":
    main()
"""
    return deployment_script

def main():
    """Main execution."""
    print("=" * 70)
    print("DEPLOY FREERIDEINVESTOR.COM MENU NAVIGATION FIX")
    print("=" * 70)
    print()
    
    site_info = get_site_info()
    print(f"Site: freerideinvestor.com")
    print()
    
    # Read PHP fix
    php_fix = read_fix_file("freerideinvestor_menu_navigation_fix.php")
    if not php_fix:
        print("❌ Could not read PHP fix file")
        return
    
    # Extract PHP code
    php_code = php_fix
    if "```php" in php_code:
        php_code = php_code.split("```php")[1].split("```")[0].strip()
    elif "```" in php_code:
        # Try to extract if it's in a code block
        parts = php_code.split("```")
        if len(parts) > 2:
            php_code = parts[1].strip()
            if php_code.startswith("php"):
                php_code = php_code[3:].strip()
    
    # Save deployment-ready PHP code
    project_root = Path(__file__).parent.parent
    docs_dir = project_root / "docs" / "freerideinvestor"
    
    deployment_file = docs_dir / "freerideinvestor_menu_fix_DEPLOY.php"
    with open(deployment_file, 'w', encoding='utf-8') as f:
        f.write(php_code)
    
    print("✅ Deployment-ready PHP code saved:")
    print(f"   {deployment_file.relative_to(project_root)}")
    print()
    print("=" * 70)
    print("DEPLOYMENT INSTRUCTIONS")
    print("=" * 70)
    print()
    print("To deploy this fix:")
    print()
    print("1. Access freerideinvestor.com WordPress installation")
    print("2. Navigate to: wp-content/themes/[theme-name]/functions.php")
    print("3. Add the code from the deployment file to the end of functions.php")
    print("4. Save the file")
    print("5. Clear WordPress cache (if using caching plugin)")
    print("6. Test menu navigation:")
    print("   - Click menu toggle button (mobile)")
    print("   - Test all navigation links (Home, Blog, About, Contact)")
    print("   - Verify menu closes when clicking outside")
    print()
    print("File to deploy:")
    print(f"   {deployment_file.relative_to(project_root)}")
    print()
    print("⚠️  Note: This requires SFTP/SSH access or WordPress file editor access")
    print()
    print("✅ Deployment package ready")

if __name__ == "__main__":
    main()

