#!/usr/bin/env python3
"""
Website Fixes Deployment Script
================================

Automates deployment of website fixes to live sites.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-29
"""

import os
import sys
from pathlib import Path
from typing import Dict, List, Optional


class WebsiteFixDeployer:
    """Deploys website fixes to live sites."""
    
    def __init__(self):
        self.base_path = Path(__file__).parent.parent
        self.fixes = {
            'FreeRideInvestor': {
                'files': [
                    'css/styles/base/_typography.css',
                    'css/styles/base/_variables.css',
                    'functions.php'
                ],
                'deployment_method': 'wordpress',
                'target_path': '/wp-content/themes/freerideinvestor/'
            },
            'prismblossom.online': {
                'files': [
                    'wordpress-theme/prismblossom/functions.php',
                    'wordpress-theme/prismblossom/page-carmyn.php'
                ],
                'deployment_method': 'wordpress',
                'target_path': '/wp-content/themes/prismblossom/'
            },
            'southwestsecret.com': {
                'files': [
                    'css/style.css',
                    'wordpress-theme/southwestsecret/functions.php'
                ],
                'deployment_method': 'wordpress',
                'target_path': '/wp-content/themes/southwestsecret/'
            }
        }
    
    def verify_files_exist(self, site: str) -> List[str]:
        """Verify all fix files exist locally."""
        missing = []
        site_fixes = self.fixes.get(site, {})
        
        for file_path in site_fixes.get('files', []):
            full_path = self.base_path / site / file_path
            if not full_path.exists():
                missing.append(str(full_path))
        
        return missing
    
    def generate_deployment_instructions(self, site: str) -> str:
        """Generate deployment instructions for a site."""
        site_fixes = self.fixes.get(site, {})
        instructions = []
        
        instructions.append(f"\n{'='*60}")
        instructions.append(f"📦 DEPLOYMENT INSTRUCTIONS: {site}")
        instructions.append(f"{'='*60}\n")
        
        instructions.append("Files to Deploy:")
        for file_path in site_fixes.get('files', []):
            source = self.base_path / site / file_path
            target = site_fixes.get('target_path', '') + file_path
            instructions.append(f"  📄 {file_path}")
            instructions.append(f"     Source: {source}")
            instructions.append(f"     Target: {target}\n")
        
        instructions.append("Deployment Steps:")
        instructions.append("  1. Backup current files on live server")
        instructions.append("  2. Upload files via FTP/SFTP or WordPress admin")
        instructions.append("  3. Clear WordPress cache")
        instructions.append("  4. Clear browser cache")
        instructions.append("  5. Verify fixes using: python tools/verify_website_fixes.py")
        
        return "\n".join(instructions)
    
    def create_deployment_package(self, site: str) -> bool:
        """Create a deployment package (zip file) for a site."""
        try:
            import zipfile
            from datetime import datetime
            
            site_fixes = self.fixes.get(site, {})
            package_name = f"{site}_fixes_{datetime.now().strftime('%Y%m%d_%H%M%S')}.zip"
            package_path = self.base_path / 'tools' / 'deployment_packages' / package_name
            
            # Create deployment_packages directory if it doesn't exist
            package_path.parent.mkdir(parents=True, exist_ok=True)
            
            with zipfile.ZipFile(package_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
                for file_path in site_fixes.get('files', []):
                    source = self.base_path / site / file_path
                    if source.exists():
                        zipf.write(source, file_path)
                        print(f"  ✅ Added: {file_path}")
                    else:
                        print(f"  ❌ Missing: {file_path}")
                        return False
            
            print(f"\n✅ Deployment package created: {package_path}")
            return True
        except Exception as e:
            print(f"❌ Error creating package: {e}")
            return False
    
    def deploy_all(self) -> Dict[str, bool]:
        """Generate deployment instructions for all sites."""
        results = {}
        
        print("\n" + "="*60)
        print("🚀 WEBSITE FIXES DEPLOYMENT")
        print("="*60)
        
        for site in self.fixes.keys():
            print(f"\n📋 Processing: {site}")
            
            # Verify files exist
            missing = self.verify_files_exist(site)
            if missing:
                print(f"  ❌ Missing files: {missing}")
                results[site] = False
                continue
            
            print(f"  ✅ All files verified")
            
            # Generate instructions
            instructions = self.generate_deployment_instructions(site)
            print(instructions)
            
            # Create deployment package
            print(f"\n📦 Creating deployment package...")
            if self.create_deployment_package(site):
                results[site] = True
            else:
                results[site] = False
        
        return results


def main():
    """Main execution."""
    deployer = WebsiteFixDeployer()
    results = deployer.deploy_all()
    
    print("\n" + "="*60)
    print("📊 DEPLOYMENT SUMMARY")
    print("="*60)
    
    for site, success in results.items():
        status = "✅ Ready" if success else "❌ Failed"
        print(f"  {status}: {site}")
    
    print("\n💡 Next Steps:")
    print("  1. Review deployment instructions above")
    print("  2. Deploy packages to live sites")
    print("  3. Run verification: python tools/verify_website_fixes.py")
    print("  4. Monitor for issues")
    
    return 0 if all(results.values()) else 1


if __name__ == '__main__':
    exit(main())

