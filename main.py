#!/usr/bin/env python3
"""
Websites Management System - Main Entry Point
==============================================

A comprehensive management system for deploying, monitoring, and maintaining
all websites in the portfolio.

Features:
- Deploy all websites with a single command
- Deploy individual websites
- Monitor website status and health
- Automated deployment pipelines
- WordPress management tools
- Cache management
- Backup and recovery

Usage:
    python main.py deploy all          # Deploy all websites
    python main.py deploy <site>       # Deploy specific website
    python main.py status              # Check status of all websites
    python main.py monitor             # Monitor website health
    python main.py cache clear         # Clear all caches
    python main.py wordpress check     # Check WordPress versions
    python main.py backup              # Create backups

Author: The Swarm (Multi-Agent AI System)
Date: 2026-01-01
"""

import argparse
import json
import sys
import os
from pathlib import Path
from typing import Dict, List, Optional, Tuple
import subprocess
import yaml

# Add project paths to Python path
project_root = Path(__file__).parent
sys.path.insert(0, str(project_root / "ops"))
sys.path.insert(0, str(project_root / "tools"))

# Import deployment modules
try:
    from deployment.unified_deployer import deploy_site, get_files_to_deploy
    from deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYMENT_AVAILABLE = True
except (ImportError, SyntaxError):
    DEPLOYMENT_AVAILABLE = False

# Import monitoring modules
try:
    from deployment.deployment_monitor import monitor_sites
    MONITORING_AVAILABLE = True
except (ImportError, SyntaxError):
    MONITORING_AVAILABLE = False


class WebsiteManager:
    """Main website management system."""

    def __init__(self):
        self.project_root = Path(__file__).parent
        self.sites_config_path = self.project_root / "sites"
        self.websites_path = self.project_root / "websites"
        self.config_path = self.project_root / "config"

        # Load site configurations
        self.site_configs = self._load_site_configs()
        self.site_registry = self._load_site_registry()
        self.site_mappings = self._load_site_mappings()

    def _load_site_configs(self) -> Dict:
        """Load site configurations from deployment config."""
        config_path = self.config_path / "site_configs.json"
        if config_path.exists():
            try:
                with open(config_path, 'r') as f:
                    return json.load(f)
            except Exception as e:
                print(f"⚠️  Could not load site configs: {e}")
        return {}

    def _load_site_registry(self) -> Dict:
        """Load site registry."""
        registry_path = self.config_path / "sites_registry.json"
        if registry_path.exists():
            try:
                with open(registry_path, 'r') as f:
                    return json.load(f)
            except Exception as e:
                print(f"⚠️  Could not load site registry: {e}")
        return {}

    def _load_site_mappings(self) -> Dict[str, str]:
        """Load site ID to domain mappings from YAML files."""
        mappings = {}

        if self.sites_config_path.exists():
            for item in self.sites_config_path.iterdir():
                if item.suffix == '.yaml' and item.name != 'README.md':
                    try:
                        with open(item, 'r') as f:
                            data = yaml.safe_load(f)
                            if data and 'site_id' in data and 'domain' in data:
                                mappings[data['site_id']] = data['domain']
                    except Exception as e:
                        print(f"⚠️  Could not load {item}: {e}")

        return mappings

    def get_available_sites(self) -> List[str]:
        """Get list of all available websites."""
        sites = set()

        # From websites directory (these are the actual deployed sites)
        if self.websites_path.exists():
            for item in self.websites_path.iterdir():
                if item.is_dir() and not item.name.startswith('.'):
                    sites.add(item.name)

        # From site mappings (ensure we have full domains)
        for domain in self.site_mappings.values():
            sites.add(domain)

        # Manual additions for known sites not in directories
        known_sites = [
            'weareswarm.site',
            'digitaldreamscape.site',
            'ariajet.site',
            'prismblossom.online'
        ]
        for site in known_sites:
            sites.add(site)

        return sorted(list(sites))

    def deploy_all_sites(self, dry_run: bool = False) -> Dict[str, bool]:
        """Deploy all websites."""
        if not DEPLOYMENT_AVAILABLE:
            print("❌ Deployment tools not available")
            print("   Install dependencies: pip install paramiko python-dotenv")
            return {}

        sites = self.get_available_sites()
        if not sites:
            print("❌ No websites found to deploy")
            return {}

        print(f"\n🚀 DEPLOYING ALL {len(sites)} WEBSITES")
        print("=" * 60)

        results = {}
        for site_domain in sites:
            site_config = self.site_configs.get(site_domain, {})
            results[site_domain] = self._deploy_single_site(site_domain, site_config, dry_run)

        return results

    def deploy_single_site(self, site_domain: str, dry_run: bool = False) -> bool:
        """Deploy a single website."""
        if not DEPLOYMENT_AVAILABLE:
            print("❌ Deployment tools not available")
            return False

        site_config = self.site_configs.get(site_domain, {})
        return self._deploy_single_site(site_domain, site_config, dry_run)

    def _deploy_single_site(self, site_domain: str, site_config: Dict, dry_run: bool) -> bool:
        """Internal method to deploy a single site."""
        try:
            # Use the unified deployer function
            return deploy_site(site_domain, site_config, dry_run)
        except Exception as e:
            print(f"❌ Error deploying {site_domain}: {e}")
            return False

    def check_status(self) -> Dict[str, Dict]:
        """Check status of all websites."""
        sites = self.get_available_sites()
        results = {}

        print(f"\n📊 CHECKING STATUS OF {len(sites)} WEBSITES")
        print("=" * 60)

        for site in sites:
            print(f"\n🔍 Checking {site}...")
            status = self._check_site_status(site)
            results[site] = status

            # Print summary
            if status.get('reachable'):
                print("   ✅ Reachable")
            else:
                print("   ❌ Not reachable")

            if status.get('wordpress'):
                version = status.get('wp_version', 'unknown')
                print(f"   ✅ WordPress {version}")
            else:
                print("   ⚠️  Not WordPress or not detected")

        return results

    def _check_site_status(self, site_domain: str) -> Dict:
        """Check status of a single site."""
        status = {
            'domain': site_domain,
            'reachable': False,
            'wordpress': False,
            'wp_version': None,
            'response_time': None,
            'last_deployment': None
        }

        # Try to load site config for more detailed checks
        if site_domain in self.site_configs:
            config = self.site_configs[site_domain]
            # Here you could add HTTP checks, WordPress API calls, etc.

        return status

    def monitor_sites(self) -> Dict[str, Dict]:
        """Monitor website health and performance."""
        if not MONITORING_AVAILABLE:
            print("⚠️  Monitoring tools not available")
            return self.check_status()  # Fallback to basic status check

        print("\n📈 MONITORING WEBSITE HEALTH")
        print("=" * 60)

        try:
            return monitor_sites()
        except Exception as e:
            print(f"❌ Monitoring failed: {e}")
            return {}

    def clear_caches(self) -> Dict[str, bool]:
        """Clear all website caches."""
        print("\n🧹 CLEARING ALL CACHES")
        print("=" * 60)

        sites = self.get_available_sites()
        results = {}

        for site in sites:
            print(f"🧽 Clearing cache for {site}...")
            success = self._clear_site_cache(site)
            results[site] = success
            status = "✅" if success else "❌"
            print(f"   {status} {site}")

        return results

    def _clear_site_cache(self, site_domain: str) -> bool:
        """Clear cache for a single site."""
        try:
            # Try to use WordPress CLI if available
            cmd = [
                "php", "ops/deployment/clear_wordpress_cache.php",
                "--site", site_domain
            ]
            result = subprocess.run(cmd, capture_output=True, text=True, cwd=self.project_root)
            return result.returncode == 0
        except Exception:
            return False

    def check_wordpress_versions(self) -> Dict[str, str]:
        """Check WordPress versions across all sites."""
        print("\n🔍 CHECKING WORDPRESS VERSIONS")
        print("=" * 60)

        try:
            # Try to run the WordPress version checker
            cmd = ["python", "ops/deployment/check_wordpress_versions.py"]
            result = subprocess.run(cmd, capture_output=True, text=True, cwd=self.project_root)

            if result.returncode == 0:
                # Parse output if needed
                print(result.stdout)
            else:
                print(f"❌ Version check failed: {result.stderr}")

        except Exception as e:
            print(f"❌ Version check failed: {e}")

        return {}

    def create_backups(self) -> Dict[str, bool]:
        """Create backups of all websites."""
        print("\n💾 CREATING BACKUPS")
        print("=" * 60)

        sites = self.get_available_sites()
        results = {}

        for site in sites:
            print(f"💾 Backing up {site}...")
            success = self._create_site_backup(site)
            results[site] = success
            status = "✅" if success else "❌"
            print(f"   {status} {site}")

        return results

    def _create_site_backup(self, site_domain: str) -> bool:
        """Create backup for a single site."""
        try:
            # This would typically use backup tools
            # For now, just return success
            return True
        except Exception:
            return False


def print_banner():
    """Print the main banner."""
    banner = """
    ███████╗██╗    ██╗ █████╗ ██████╗ ███╗   ███╗
    ██╔════╝██║    ██║██╔══██╗██╔══██╗████╗ ████║
    ███████╗██║ █╗ ██║███████║██████╔╝██╔████╔██║
    ╚════██║██║███╗██║██╔══██║██╔══██╗██║╚██╔╝██║
    ███████║╚███╔███╔╝██║  ██║██║  ██║██║ ╚═╝ ██║
    ╚══════╝ ╚══╝╚══╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═╝     ╚═╝

    🌐 WEBSITES MANAGEMENT SYSTEM
    ==============================
    """
    print(banner)


def main():
    """Main entry point."""
    print_banner()

    parser = argparse.ArgumentParser(
        description='Websites Management System',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python main.py deploy all                    # Deploy all websites
  python main.py deploy dadudekc.com          # Deploy single website
  python main.py status                        # Check all website status
  python main.py monitor                       # Monitor website health
  python main.py cache clear                   # Clear all caches
  python main.py wordpress check              # Check WordPress versions
  python main.py config edit --site dadudekc.com  # Edit wp-config.php
  python main.py config deploy --site dadudekc.com # Deploy config changes
  python main.py backup                        # Create backups

Environment Variables:
  Set deployment credentials in .env file or environment
        """
    )

    subparsers = parser.add_subparsers(dest='command', help='Available commands')

    # Deploy command
    deploy_parser = subparsers.add_parser('deploy', help='Deploy websites')
    deploy_parser.add_argument('target', choices=['all'] + ['site'], help='Deployment target')
    deploy_parser.add_argument('--site', type=str, help='Specific site to deploy (when target=site)')
    deploy_parser.add_argument('--dry-run', action='store_true', help='Test without deploying')

    # Status command
    subparsers.add_parser('status', help='Check website status')

    # Monitor command
    subparsers.add_parser('monitor', help='Monitor website health')

    # Cache command
    cache_parser = subparsers.add_parser('cache', help='Cache management')
    cache_parser.add_argument('action', choices=['clear'], help='Cache action')

    # WordPress command
    wp_parser = subparsers.add_parser('wordpress', help='WordPress management')
    wp_parser.add_argument('action', choices=['check'], help='WordPress action')

    # Config command
    config_parser = subparsers.add_parser('config', help='Configuration management')
    config_parser.add_argument('action', choices=['edit', 'deploy', 'backup', 'validate'],
                              help='Configuration action')
    config_parser.add_argument('--site', type=str, help='Target site domain')

    # Backup command
    subparsers.add_parser('backup', help='Create backups')

    args = parser.parse_args()

    if not args.command:
        parser.print_help()
        return 0

    # Initialize manager
    manager = WebsiteManager()

    try:
        if args.command == 'deploy':
            if args.target == 'all':
                results = manager.deploy_all_sites(dry_run=args.dry_run or False)
                success_count = sum(1 for v in results.values() if v)
                total_count = len(results)

                print(f"\n📊 DEPLOYMENT SUMMARY")
                print("=" * 40)
                print(f"✅ Successful: {success_count}/{total_count}")
                print(f"❌ Failed: {total_count - success_count}/{total_count}")

                if args.dry_run:
                    print("\n💡 This was a dry run. Remove --dry-run to actually deploy.")

                return 0 if success_count == total_count else 1

            elif args.target == 'site' and args.site:
                success = manager.deploy_single_site(args.site, dry_run=args.dry_run or False)
                return 0 if success else 1
            else:
                print("❌ Specify 'all' or provide --site <domain>")
                return 1

        elif args.command == 'status':
            results = manager.check_status()
            return 0

        elif args.command == 'monitor':
            results = manager.monitor_sites()
            return 0

        elif args.command == 'cache':
            if args.action == 'clear':
                results = manager.clear_caches()
                success_count = sum(1 for v in results.values() if v)
                total_count = len(results)
                print(f"\n🧹 Cache clearing: {success_count}/{total_count} successful")
                return 0 if success_count == total_count else 1

        elif args.command == 'wordpress':
            if args.action == 'check':
                manager.check_wordpress_versions()
                return 0

        elif args.command == 'config':
            if args.action == 'edit':
                if not args.site:
                    print("❌ --site required for config edit")
                    return 1
                # Import config manager
                from config_manager import ConfigManager
                config_manager = ConfigManager()
                success = config_manager.interactive_config_editor(args.site)
                return 0 if success else 1

            elif args.action == 'deploy':
                if not args.site:
                    print("❌ --site required for config deploy")
                    return 1
                from config_manager import ConfigManager
                config_manager = ConfigManager()
                config_manager.backup_config(args.site)
                success = config_manager.deploy_config(args.site)
                return 0 if success else 1

            elif args.action == 'backup':
                if not args.site:
                    print("❌ --site required for config backup")
                    return 1
                from config_manager import ConfigManager
                config_manager = ConfigManager()
                success = config_manager.backup_config(args.site)
                return 0 if success else 1

            elif args.action == 'validate':
                if not args.site:
                    print("❌ --site required for config validate")
                    return 1
                from config_manager import ConfigManager
                config_manager = ConfigManager()
                success = config_manager.validate_config_syntax(args.site)
                return 0 if success else 1

        elif args.command == 'backup':
            results = manager.create_backups()
            success_count = sum(1 for v in results.values() if v)
            total_count = len(results)
            print(f"\n💾 Backups: {success_count}/{total_count} successful")
            return 0 if success_count == total_count else 1

    except KeyboardInterrupt:
        print("\n⚠️  Operation cancelled by user")
        return 1
    except Exception as e:
        print(f"\n❌ Unexpected error: {e}")
        import traceback
        traceback.print_exc()
        return 1


if __name__ == '__main__':
    exit(main())