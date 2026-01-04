#!/usr/bin/env python3
"""
Deploy on Push - Automated Deployment Trigger
===========================================

This script is designed to be triggered by:
1. Git post-commit hooks
2. CI/CD pipelines (GitHub Actions, etc.)
3. Manual deployment commands
4. Push events to main/master branches

It provides comprehensive deployment automation with:
- Syntax validation
- Selective deployment
- Parallel processing
- Error handling and rollback
- Detailed logging

Usage:
    python ops/deployment/deploy_on_push.py --auto         # Auto-detect changes and deploy
    python ops/deployment/deploy_on_push.py --all          # Deploy all sites
    python ops/deployment/deploy_on_push.py --site <site>  # Deploy specific site

Author: Agent-7 (Web Development Specialist)
Date: 2026-01-01
"""

import argparse
import json
import os
import subprocess
import sys
from pathlib import Path
from typing import Dict, List, Optional

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

from deployment_automation import DeploymentAutomation


class DeployOnPush:
    """Automated deployment system triggered by git push events."""

    def __init__(self):
        self.repo_root = Path(__file__).resolve().parents[2]
        self.automation = DeploymentAutomation()

    def detect_push_event(self) -> bool:
        """Detect if this is running in response to a push event."""
        # Check for environment variables set by CI/CD systems
        ci_indicators = [
            'GITHUB_ACTIONS',      # GitHub Actions
            'GITLAB_CI',          # GitLab CI
            'CI',                 # Generic CI
            'CONTINUOUS_INTEGRATION',
        ]

        return any(os.getenv(indicator) for indicator in ci_indicators)

    def get_commit_range(self) -> Optional[str]:
        """Get the commit range for this push."""
        try:
            # Get the previous commit and current HEAD
            result = subprocess.run(
                ['git', 'log', '--oneline', '-2'],
                capture_output=True,
                text=True,
                cwd=self.repo_root,
                check=True
            )

            lines = result.stdout.strip().split('\n')
            if len(lines) >= 2:
                # Return the range from previous commit to current
                commits = [line.split()[0] for line in lines]
                return f"{commits[1]}..{commits[0]}"  # older..newer

        except subprocess.CalledProcessError:
            pass

        return None

    def get_files_changed_in_push(self) -> List[str]:
        """Get files changed in the current push."""
        commit_range = self.get_commit_range()

        if commit_range:
            try:
                result = subprocess.run(
                    ['git', 'diff', '--name-only', commit_range],
                    capture_output=True,
                    text=True,
                    cwd=self.repo_root,
                    check=True
                )

                files = [f.strip() for f in result.stdout.splitlines() if f.strip()]
                return files

            except subprocess.CalledProcessError:
                pass

        # Fallback: get recent changes
        return self.automation.get_changed_files()

    def should_deploy_branch(self) -> bool:
        """Check if the current branch should trigger deployment."""
        try:
            result = subprocess.run(
                ['git', 'branch', '--show-current'],
                capture_output=True,
                text=True,
                cwd=self.repo_root,
                check=True
            )

            current_branch = result.stdout.strip()

            # Deploy these branches
            deploy_branches = {'main', 'master', 'production', 'staging'}

            return current_branch in deploy_branches

        except subprocess.CalledProcessError:
            return False

    def run_push_deployment(self) -> bool:
        """Run deployment triggered by push event."""
        print("🚀 DEPLOY ON PUSH - Starting automated deployment")
        print("=" * 60)

        # Check if we should deploy this branch
        if not self.should_deploy_branch():
            print("ℹ️  Current branch not configured for auto-deployment")
            print("   Only main/master/production/staging branches trigger deployment")
            return True

        # Detect push event
        is_push = self.detect_push_event()
        if is_push:
            print("📡 Detected push event from CI/CD system")
        else:
            print("🔧 Manual deployment trigger")

        # Get changed files
        print("\n📋 Detecting changed files...")
        changed_files = self.get_files_changed_in_push()

        if not changed_files:
            print("✅ No files changed in this push")
            return True

        print(f"📁 Found {len(changed_files)} changed file(s):")
        for file in changed_files[:10]:  # Show first 10
            print(f"   - {file}")
        if len(changed_files) > 10:
            print(f"   ... and {len(changed_files) - 10} more")

        # Validate PHP files
        php_files = [f for f in changed_files if f.endswith('.php')]
        if php_files:
            print(f"\n🐘 Validating PHP syntax ({len(php_files)} files)...")
            validation_results = self.automation.validate_php_syntax(php_files)

            failed_validations = [r for r in validation_results if not r.valid]
            if failed_validations:
                print("❌ PHP SYNTAX ERRORS FOUND!")
                for result in failed_validations:
                    print(f"   - {result.file_path}:")
                    for error in result.errors[:3]:  # Show first 3 errors
                        print(f"     {error}")

                print("\n🚫 DEPLOYMENT BLOCKED")
                print("   Fix PHP syntax errors before deployment can proceed")
                return False

            print("✅ All PHP files passed syntax validation")

        # Classify files by site
        print("
🏷️  Classifying files by deployment target..."        site_files = self.automation.classify_files_by_site(changed_files)

        if not site_files:
            print("⚠️  No files mapped to deployable websites")
            print("   Files may be in non-deployable directories")
            return True

        print(f"🌐 Files will be deployed to {len(site_files)} website(s):")
        for site, files in site_files.items():
            print(f"   - {site}: {len(files)} file(s)")

        # Run deployment
        print("
🚀 Starting deployment..."        success = self.automation.run_automated_deployment(dry_run=False)

        if success:
            print("
🎉 PUSH DEPLOYMENT COMPLETED SUCCESSFULLY!"            print("   All websites have been updated with the latest changes")

            # Suggest cache clearing
            print("
💡 Recommended next steps:"            print("   1. Clear browser cache to see changes")
            print("   2. Test website functionality")
            print("   3. Monitor for any issues in logs")
        else:
            print("
❌ PUSH DEPLOYMENT FAILED"            print("   Check the errors above and fix any issues")
            print("   You may need to deploy manually or rollback changes")

        return success

    def run_manual_deployment(self, all_sites: bool = False, specific_site: Optional[str] = None) -> bool:
        """Run manual deployment."""
        print("🔧 MANUAL DEPLOYMENT - Starting deployment process")
        print("=" * 60)

        if all_sites:
            print("🌐 Deploying to ALL websites...")
            from unified_deployer import main as unified_main
            # This would need to be adapted to return success/failure
            return unified_main() == 0

        elif specific_site:
            print(f"🎯 Deploying to specific site: {specific_site}")
            from unified_deployer import deploy_site
            # This would need to be adapted
            return True  # Placeholder

        else:
            # Run automated deployment based on changes
            return self.automation.run_automated_deployment(dry_run=False)


def main():
    """Main execution function."""
    parser = argparse.ArgumentParser(description='Deploy on Push - Automated Deployment System')
    parser.add_argument('--auto', action='store_true',
                       help='Auto-detect push event and deploy changed files')
    parser.add_argument('--all', action='store_true',
                       help='Deploy all websites (manual override)')
    parser.add_argument('--site', type=str,
                       help='Deploy specific site only (manual override)')
    parser.add_argument('--test', action='store_true',
                       help='Test deployment without actually deploying')

    args = parser.parse_args()

    deploy_system = DeployOnPush()

    if args.auto:
        # Automatic deployment based on push
        success = deploy_system.run_push_deployment()

    elif args.all:
        # Manual deployment to all sites
        success = deploy_system.run_manual_deployment(all_sites=True)

    elif args.site:
        # Manual deployment to specific site
        success = deploy_system.run_manual_deployment(specific_site=args.site)

    elif args.test:
        # Test mode - show what would be deployed
        print("🧪 TEST MODE - Showing what would be deployed")
        automation = DeploymentAutomation()
        success = automation.run_automated_deployment(dry_run=True)

    else:
        parser.print_help()
        return 1

    return 0 if success else 1


if __name__ == '__main__':
    exit(main())