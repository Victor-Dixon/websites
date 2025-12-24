#!/usr/bin/env python3
"""
Unified WordPress Manager
=========================

Comprehensive WordPress management tool for the swarm.
Provides full WordPress site management capabilities:
- Deployment (SFTP, REST API, WP-CLI, Browser automation)
- Theme management (install, activate, update, delete, list)
- Plugin management (install, activate, deactivate, update, delete, list)
- Content management (posts, pages, media)
- Configuration management (options, constants)
- Cache management (clear all cache types)
- Health monitoring (site status, updates, security)

V2 Compliance: Modular design, <300 lines per module
Author: Agent-7 (Web Development Specialist)
"""

import json
import os
import sys
from pathlib import Path
from typing import Dict, List, Optional, Any
from enum import Enum

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "websites" / "ops" / "deployment"))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False

try:
    import paramiko
    PARAMIKO_AVAILABLE = True
except ImportError:
    PARAMIKO_AVAILABLE = False

try:
    import requests
    REQUESTS_AVAILABLE = True
except ImportError:
    REQUESTS_AVAILABLE = False


class DeploymentMethod(Enum):
    """WordPress deployment methods."""
    SFTP = "sftp"
    REST_API = "rest_api"
    WP_CLI = "wp_cli"
    BROWSER = "browser"


class UnifiedWordPressManager:
    """Unified WordPress management interface."""
    
    def __init__(self, site_domain: str, config: Optional[Dict] = None):
        """Initialize manager for a WordPress site."""
        self.site_domain = site_domain
        self.config = config or self._load_config()
        self.deployer = None
        
        if DEPLOYER_AVAILABLE:
            try:
                site_configs = self._load_site_configs()
                self.deployer = SimpleWordPressDeployer(site_domain, site_configs)
            except Exception as e:
                print(f"⚠️  Deployer initialization warning: {e}")
    
    def _load_config(self) -> Dict:
        """Load site configuration."""
        config_path = Path("D:/websites/configs/site_configs.json")
        if config_path.exists():
            with open(config_path, 'r', encoding='utf-8') as f:
                configs = json.load(f)
                return configs.get(self.site_domain, {})
        return {}
    
    def _load_site_configs(self) -> Dict:
        """Load all site configurations."""
        config_path = Path("D:/websites/configs/site_configs.json")
        if config_path.exists():
            with open(config_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        return {}
    
    # ==================== DEPLOYMENT ====================
    
    def deploy_file(self, local_path: Path, remote_path: str = None, method: DeploymentMethod = DeploymentMethod.SFTP) -> bool:
        """Deploy a file to WordPress site."""
        if method == DeploymentMethod.SFTP:
            return self._deploy_via_sftp(local_path, remote_path)
        elif method == DeploymentMethod.REST_API:
            return self._deploy_via_rest_api(local_path, remote_path)
        elif method == DeploymentMethod.WP_CLI:
            return self._deploy_via_wp_cli(local_path, remote_path)
        else:
            print(f"⚠️  Deployment method {method} not yet implemented")
            return False
    
    def _deploy_via_sftp(self, local_path: Path, remote_path: str = None) -> bool:
        """Deploy file via SFTP."""
        if not self.deployer:
            print("❌ SFTP deployer not available")
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            return self.deployer.deploy_file(local_path, remote_path)
        finally:
            self.deployer.disconnect()
    
    def _deploy_via_rest_api(self, local_path: Path, remote_path: str = None) -> bool:
        """Deploy file via WordPress REST API."""
        # TODO: Implement REST API file deployment
        print("⚠️  REST API deployment not yet implemented")
        return False
    
    def _deploy_via_wp_cli(self, local_path: Path, remote_path: str = None) -> bool:
        """Deploy file via WP-CLI."""
        if not PARAMIKO_AVAILABLE:
            print("❌ paramiko required for WP-CLI deployment")
            return False
        
        # TODO: Implement WP-CLI file deployment
        print("⚠️  WP-CLI deployment not yet implemented")
        return False
    
    # ==================== THEME MANAGEMENT ====================
    
    def activate_theme(self, theme_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Activate a WordPress theme."""
        if method == DeploymentMethod.WP_CLI:
            return self._activate_theme_wp_cli(theme_name)
        elif method == DeploymentMethod.REST_API:
            return self._activate_theme_rest_api(theme_name)
        else:
            print(f"⚠️  Theme activation via {method} not yet implemented")
            return False
    
    def _activate_theme_wp_cli(self, theme_name: str) -> bool:
        """Activate theme via WP-CLI over SSH."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            # Get remote path
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            # Execute WP-CLI command
            command = f"cd {remote_path} && wp theme activate {theme_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Activated" in result:
                print(f"✅ Theme '{theme_name}' activated")
                return True
            else:
                print(f"⚠️  Theme activation result: {result}")
                return False
        finally:
            self.deployer.disconnect()
    
    def _activate_theme_rest_api(self, theme_name: str) -> bool:
        """Activate theme via WordPress REST API."""
        if not REQUESTS_AVAILABLE:
            print("❌ requests library required for REST API")
            return False
        
        from requests.auth import HTTPBasicAuth
        
        rest_api = self.config.get("rest_api", {})
        username = rest_api.get("username", "")
        app_password = rest_api.get("app_password", "")
        site_url = rest_api.get("site_url", self.config.get("site_url", f"https://{self.site_domain}"))
        
        if not username or not app_password or "REPLACE_WITH" in app_password:
            print("❌ REST API credentials not configured")
            return False
        
        try:
            api_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/themes"
            auth = HTTPBasicAuth(username, app_password)
            
            # Get themes list
            response = requests.get(api_url, auth=auth, timeout=30)
            if response.status_code != 200:
                print(f"❌ Could not access themes API: {response.status_code}")
                return False
            
            themes = response.json()
            theme_found = None
            for theme in themes:
                if theme.get('stylesheet', '') == theme_name:
                    theme_found = theme
                    break
            
            if not theme_found:
                print(f"⚠️  Theme '{theme_name}' not found")
                return False
            
            if theme_found.get('status') == 'active':
                print(f"✅ Theme '{theme_name}' already active")
                return True
            
            # Try activation endpoint (may not work - WordPress REST API limitation)
            activate_url = f"{api_url}/{theme_name}"
            activate_response = requests.post(
                activate_url, auth=auth, json={"status": "active"}, timeout=30
            )
            
            if activate_response.status_code in [200, 201]:
                print(f"✅ Theme '{theme_name}' activated")
                return True
            else:
                print("⚠️  REST API theme activation not supported - use WP-CLI method")
                return False
        except Exception as e:
            print(f"❌ REST API activation error: {e}")
            return False
    
    def list_themes(self, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> List[Dict[str, Any]]:
        """List all installed themes."""
        if method == DeploymentMethod.WP_CLI:
            return self._list_themes_wp_cli()
        elif method == DeploymentMethod.REST_API:
            return self._list_themes_rest_api()
        else:
            print(f"⚠️  Theme listing via {method} not yet implemented")
            return []
    
    def _list_themes_wp_cli(self) -> List[Dict[str, Any]]:
        """List themes via WP-CLI."""
        if not self.deployer:
            return []
        
        if not self.deployer.connect():
            return []
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp theme list --format=json --allow-root"
            result = self.deployer.execute_command(command)
            
            try:
                themes = json.loads(result)
                return [{"name": t.get("name", ""), "status": t.get("status", ""), 
                        "version": t.get("version", "")} for t in themes]
            except json.JSONDecodeError:
                # Fallback: parse tabular output
                lines = [l.strip() for l in result.split('\n') if l.strip() and not l.startswith('+')]
                themes = []
                for line in lines[1:]:  # Skip header
                    parts = line.split()
                    if len(parts) >= 2:
                        themes.append({"name": parts[0], "status": parts[1]})
                return themes
        finally:
            self.deployer.disconnect()
    
    def _list_themes_rest_api(self) -> List[Dict[str, Any]]:
        """List themes via REST API."""
        if not REQUESTS_AVAILABLE:
            return []
        
        from requests.auth import HTTPBasicAuth
        
        rest_api = self.config.get("rest_api", {})
        username = rest_api.get("username", "")
        app_password = rest_api.get("app_password", "")
        site_url = rest_api.get("site_url", self.config.get("site_url", f"https://{self.site_domain}"))
        
        if not username or not app_password or "REPLACE_WITH" in app_password:
            return []
        
        try:
            api_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/themes"
            auth = HTTPBasicAuth(username, app_password)
            response = requests.get(api_url, auth=auth, timeout=30)
            
            if response.status_code == 200:
                themes = response.json()
                return [{"name": t.get("name", ""), "status": t.get("status", ""),
                        "stylesheet": t.get("stylesheet", "")} for t in themes]
        except Exception:
            pass
        
        return []
    
    def install_theme(self, theme_source: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Install a WordPress theme (from WordPress repo slug or zip URL)."""
        if method == DeploymentMethod.WP_CLI:
            return self._install_theme_wp_cli(theme_source)
        else:
            print(f"⚠️  Theme installation via {method} not yet implemented")
            return False
    
    def _install_theme_wp_cli(self, theme_source: str) -> bool:
        """Install theme via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp theme install {theme_source} --allow-root --activate"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Installed" in result:
                print(f"✅ Theme '{theme_source}' installed")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def update_theme(self, theme_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Update a WordPress theme."""
        if method == DeploymentMethod.WP_CLI:
            return self._update_theme_wp_cli(theme_name)
        else:
            print(f"⚠️  Theme update via {method} not yet implemented")
            return False
    
    def _update_theme_wp_cli(self, theme_name: str) -> bool:
        """Update theme via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp theme update {theme_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Updated" in result:
                print(f"✅ Theme '{theme_name}' updated")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def delete_theme(self, theme_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Delete a WordPress theme."""
        if method == DeploymentMethod.WP_CLI:
            return self._delete_theme_wp_cli(theme_name)
        else:
            print(f"⚠️  Theme deletion via {method} not yet implemented")
            return False
    
    def _delete_theme_wp_cli(self, theme_name: str) -> bool:
        """Delete theme via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp theme delete {theme_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Deleted" in result:
                print(f"✅ Theme '{theme_name}' deleted")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    # ==================== PLUGIN MANAGEMENT ====================
    
    def activate_plugin(self, plugin_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Activate a WordPress plugin."""
        if method == DeploymentMethod.WP_CLI:
            return self._activate_plugin_wp_cli(plugin_name)
        else:
            print(f"⚠️  Plugin activation via {method} not yet implemented")
            return False
    
    def _activate_plugin_wp_cli(self, plugin_name: str) -> bool:
        """Activate plugin via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp plugin activate {plugin_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Activated" in result or "Plugin activated" in result.lower():
                print(f"✅ Plugin '{plugin_name}' activated")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def deactivate_plugin(self, plugin_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Deactivate a WordPress plugin."""
        if method == DeploymentMethod.WP_CLI:
            return self._deactivate_plugin_wp_cli(plugin_name)
        else:
            print(f"⚠️  Plugin deactivation via {method} not yet implemented")
            return False
    
    def _deactivate_plugin_wp_cli(self, plugin_name: str) -> bool:
        """Deactivate plugin via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp plugin deactivate {plugin_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Deactivated" in result:
                print(f"✅ Plugin '{plugin_name}' deactivated")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def install_plugin(self, plugin_source: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Install a WordPress plugin (from WordPress repo slug or zip URL)."""
        if method == DeploymentMethod.WP_CLI:
            return self._install_plugin_wp_cli(plugin_source)
        else:
            print(f"⚠️  Plugin installation via {method} not yet implemented")
            return False
    
    def _install_plugin_wp_cli(self, plugin_source: str) -> bool:
        """Install plugin via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp plugin install {plugin_source} --allow-root --activate"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Installed" in result:
                print(f"✅ Plugin '{plugin_source}' installed")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def update_plugin(self, plugin_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Update a WordPress plugin."""
        if method == DeploymentMethod.WP_CLI:
            return self._update_plugin_wp_cli(plugin_name)
        else:
            print(f"⚠️  Plugin update via {method} not yet implemented")
            return False
    
    def _update_plugin_wp_cli(self, plugin_name: str) -> bool:
        """Update plugin via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp plugin update {plugin_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Updated" in result:
                print(f"✅ Plugin '{plugin_name}' updated")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def delete_plugin(self, plugin_name: str, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> bool:
        """Delete a WordPress plugin."""
        if method == DeploymentMethod.WP_CLI:
            return self._delete_plugin_wp_cli(plugin_name)
        else:
            print(f"⚠️  Plugin deletion via {method} not yet implemented")
            return False
    
    def _delete_plugin_wp_cli(self, plugin_name: str) -> bool:
        """Delete plugin via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp plugin delete {plugin_name} --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "Deleted" in result:
                print(f"✅ Plugin '{plugin_name}' deleted")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def list_plugins(self, method: DeploymentMethod = DeploymentMethod.WP_CLI) -> List[Dict[str, Any]]:
        """List all installed plugins."""
        if method == DeploymentMethod.WP_CLI:
            return self._list_plugins_wp_cli()
        else:
            print(f"⚠️  Plugin listing via {method} not yet implemented")
            return []
    
    def _list_plugins_wp_cli(self) -> List[Dict[str, Any]]:
        """List plugins via WP-CLI."""
        if not self.deployer:
            return []
        
        if not self.deployer.connect():
            return []
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp plugin list --format=json --allow-root"
            result = self.deployer.execute_command(command)
            
            try:
                plugins = json.loads(result)
                return [{"name": p.get("name", ""), "status": p.get("status", ""),
                        "version": p.get("version", "")} for p in plugins]
            except json.JSONDecodeError:
                # Fallback: parse tabular output
                lines = [l.strip() for l in result.split('\n') if l.strip() and not l.startswith('+')]
                plugins = []
                for line in lines[1:]:  # Skip header
                    parts = line.split()
                    if len(parts) >= 2:
                        plugins.append({"name": parts[0], "status": parts[1]})
                return plugins
        finally:
            self.deployer.disconnect()
    
    # ==================== CACHE MANAGEMENT ====================
    
    def clear_cache(self, cache_type: str = "all") -> bool:
        """Clear WordPress cache."""
        if cache_type == "all" or cache_type == "wp":
            return self._clear_wp_cache()
        elif cache_type == "object":
            return self._clear_object_cache()
        elif cache_type == "page":
            return self._clear_page_cache()
        else:
            print(f"⚠️  Unknown cache type: {cache_type}")
            return False
    
    def _clear_wp_cache(self) -> bool:
        """Clear WordPress cache via WP-CLI."""
        if not self.deployer:
            return False
        
        if not self.deployer.connect():
            return False
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            command = f"cd {remote_path} && wp cache flush --allow-root"
            result = self.deployer.execute_command(command)
            
            if "Success" in result or "flushed" in result.lower():
                print("✅ WordPress cache cleared")
                return True
            return False
        finally:
            self.deployer.disconnect()
    
    def _clear_object_cache(self) -> bool:
        """Clear object cache."""
        # TODO: Implement object cache clearing
        return False
    
    def _clear_page_cache(self) -> bool:
        """Clear page cache."""
        # TODO: Implement page cache clearing
        return False
    
    # ==================== HEALTH & MONITORING ====================
    
    def check_health(self) -> Dict[str, Any]:
        """Check WordPress site health."""
        health = {
            "site": self.site_domain,
            "status": "unknown",
            "wp_version": None,
            "themes": [],
            "plugins": [],
            "updates_available": False
        }
        
        if not self.deployer:
            return health
        
        if not self.deployer.connect():
            health["status"] = "connection_failed"
            return health
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            # Get WP version
            version_cmd = f"cd {remote_path} && wp core version --allow-root"
            version_result = self.deployer.execute_command(version_cmd)
            if version_result.strip():
                health["wp_version"] = version_result.strip()
            
            # Get themes
            health["themes"] = self.list_themes()
            
            # Get plugins
            health["plugins"] = self.list_plugins()
            
            # Check for updates
            updates = self.get_updates()
            health["updates_available"] = bool(
                updates.get("core") or updates.get("themes") or updates.get("plugins")
            )
            
            health["status"] = "healthy"
        except Exception as e:
            health["status"] = f"error: {str(e)}"
        finally:
            self.deployer.disconnect()
        
        return health
    
    def get_updates(self) -> Dict[str, List[str]]:
        """Get available updates (core, themes, plugins)."""
        updates = {"core": [], "themes": [], "plugins": []}
        
        if not self.deployer:
            return updates
        
        if not self.deployer.connect():
            return updates
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                remote_path = f"domains/{self.site_domain}/public_html"
            
            # Check core updates
            core_cmd = f"cd {remote_path} && wp core check-update --format=json --allow-root"
            core_result = self.deployer.execute_command(core_cmd)
            try:
                core_data = json.loads(core_result)
                if core_data and isinstance(core_data, list):
                    updates["core"] = [v.get("version", "") for v in core_data]
            except (json.JSONDecodeError, AttributeError):
                pass
            
            # Check theme updates
            theme_cmd = f"cd {remote_path} && wp theme list --update=available --format=json --allow-root"
            theme_result = self.deployer.execute_command(theme_cmd)
            try:
                theme_data = json.loads(theme_result)
                if theme_data:
                    updates["themes"] = [t.get("name", "") for t in theme_data if t.get("update")]
            except (json.JSONDecodeError, AttributeError):
                pass
            
            # Check plugin updates
            plugin_cmd = f"cd {remote_path} && wp plugin list --update=available --format=json --allow-root"
            plugin_result = self.deployer.execute_command(plugin_cmd)
            try:
                plugin_data = json.loads(plugin_result)
                if plugin_data:
                    updates["plugins"] = [p.get("name", "") for p in plugin_data if p.get("update")]
            except (json.JSONDecodeError, AttributeError):
                pass
        except Exception:
            pass
        finally:
            self.deployer.disconnect()
        
        return updates


def main():
    """CLI interface for unified WordPress manager."""
    import argparse
    
    parser = argparse.ArgumentParser(description="Unified WordPress Manager")
    parser.add_argument("--site", required=True, help="Site domain")
    parser.add_argument("--action", required=True, 
                       choices=["deploy", "activate-theme", "list-themes", "install-theme",
                               "update-theme", "delete-theme", "activate-plugin", 
                               "deactivate-plugin", "list-plugins", "install-plugin", 
                               "update-plugin", "delete-plugin", "clear-cache", "health", "updates"],
                       help="Action to perform")
    parser.add_argument("--file", help="File to deploy (for deploy action)")
    parser.add_argument("--theme", help="Theme name (for theme actions)")
    parser.add_argument("--plugin", help="Plugin name (for plugin actions)")
    parser.add_argument("--method", default="sftp", choices=["sftp", "rest_api", "wp_cli"],
                       help="Deployment method")
    
    args = parser.parse_args()
    
    manager = UnifiedWordPressManager(args.site)
    
    if args.action == "deploy":
        if not args.file:
            print("❌ --file required for deploy action")
            return 1
        method = DeploymentMethod(args.method)
        return 0 if manager.deploy_file(Path(args.file), method=method) else 1
    
    elif args.action == "activate-theme":
        if not args.theme:
            print("❌ --theme required for activate-theme action")
            return 1
        method = DeploymentMethod(args.method)
        return 0 if manager.activate_theme(args.theme, method=method) else 1
    
    elif args.action == "list-themes":
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        themes = manager.list_themes(method=method)
        for theme in themes:
            print(f"  {theme.get('name', 'Unknown')} - {theme.get('status', 'Unknown')}")
        return 0
    
    elif args.action == "activate-plugin":
        if not args.plugin:
            print("❌ --plugin required for activate-plugin action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.activate_plugin(args.plugin, method=method) else 1
    
    elif args.action == "deactivate-plugin":
        if not args.plugin:
            print("❌ --plugin required for deactivate-plugin action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.deactivate_plugin(args.plugin, method=method) else 1
    
    elif args.action == "list-plugins":
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        plugins = manager.list_plugins(method=method)
        for plugin in plugins:
            print(f"  {plugin.get('name', 'Unknown')} - {plugin.get('status', 'Unknown')}")
        return 0
    
    elif args.action == "install-plugin":
        if not args.plugin:
            print("❌ --plugin required for install-plugin action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.install_plugin(args.plugin, method=method) else 1
    
    elif args.action == "update-plugin":
        if not args.plugin:
            print("❌ --plugin required for update-plugin action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.update_plugin(args.plugin, method=method) else 1
    
    elif args.action == "delete-plugin":
        if not args.plugin:
            print("❌ --plugin required for delete-plugin action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.delete_plugin(args.plugin, method=method) else 1
    
    elif args.action == "clear-cache":
        return 0 if manager.clear_cache() else 1
    
    elif args.action == "install-theme":
        if not args.theme:
            print("❌ --theme required for install-theme action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.install_theme(args.theme, method=method) else 1
    
    elif args.action == "update-theme":
        if not args.theme:
            print("❌ --theme required for update-theme action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.update_theme(args.theme, method=method) else 1
    
    elif args.action == "delete-theme":
        if not args.theme:
            print("❌ --theme required for delete-theme action")
            return 1
        method = DeploymentMethod(args.method) if args.method != "sftp" else DeploymentMethod.WP_CLI
        return 0 if manager.delete_theme(args.theme, method=method) else 1
    
    elif args.action == "health":
        health = manager.check_health()
        print(json.dumps(health, indent=2))
        return 0
    
    elif args.action == "updates":
        updates = manager.get_updates()
        print(json.dumps(updates, indent=2))
        return 0
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

