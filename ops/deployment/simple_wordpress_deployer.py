#!/usr/bin/env python3
"""
Simple WordPress Deployer
==========================

A lightweight WordPress deployment tool that uses site_configs.json
for SFTP credentials. Works without WordPressManager dependency.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import json
import sys
from pathlib import Path
from typing import Dict, Optional

try:
    import paramiko
    PARAMIKO_AVAILABLE = True
except ImportError:
    PARAMIKO_AVAILABLE = False


def load_hostinger_env_credentials():
    """Load Hostinger credentials from environment variables or .env file."""
    import os
    from dotenv import load_dotenv
    
    # Try to load .env from main repository
    env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
    if env_path.exists():
        load_dotenv(env_path)
    
    host = os.getenv("HOSTINGER_HOST")
    username = os.getenv("HOSTINGER_USER")
    password = os.getenv("HOSTINGER_PASS")
    port = int(os.getenv("HOSTINGER_PORT", "65002"))
    
    if all([host, username, password]):
        return {
            "host": host,
            "username": username,
            "password": password,
            "port": port
        }
    return None


def load_site_configs():
    """Load site configurations from multiple sources in priority order."""
    # Priority 1: Hostinger environment variables (.env)
    hostinger_creds = load_hostinger_env_credentials()
    if hostinger_creds:
        # Create a default config structure with Hostinger credentials
        # This will be used as fallback if site-specific config not found
        default_config = {
            "default": {
                "host": hostinger_creds["host"],
                "username": hostinger_creds["username"],
                "password": hostinger_creds["password"],
                "port": hostinger_creds["port"]
            }
        }
    
    # Priority 2: .deploy_credentials/sites.json (WordPressManager format)
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json_path.exists():
        try:
            with open(sites_json_path, 'r') as f:
                configs = json.load(f)
                # Merge with Hostinger defaults if available
                if hostinger_creds:
                    for site_key, site_config in configs.items():
                        if not site_config.get('host'):
                            site_config['host'] = hostinger_creds['host']
                            site_config['username'] = hostinger_creds['username']
                            site_config['password'] = hostinger_creds['password']
                            site_config['port'] = hostinger_creds['port']
                return configs
        except Exception as e:
            print(f"⚠️  Could not load sites.json: {e}")
    
    # Priority 3: site_configs.json
    config_path = Path("D:/websites/configs/site_configs.json")
    if not config_path.exists():
        config_path = Path(__file__).parent.parent.parent / "configs" / "site_configs.json"
    
    if config_path.exists():
        try:
            with open(config_path, 'r') as f:
                configs = json.load(f)
                # Merge with Hostinger defaults if available
                if hostinger_creds:
                    for site_key, site_config in configs.items():
                        sftp_config = site_config.get('sftp', {})
                        if not sftp_config.get('host'):
                            sftp_config['host'] = hostinger_creds['host']
                            sftp_config['username'] = hostinger_creds['username']
                            sftp_config['password'] = hostinger_creds['password']
                            sftp_config['port'] = hostinger_creds['port']
                return configs
        except Exception as e:
            print(f"❌ Could not load site_configs.json: {e}")
    
    # Priority 4: Return Hostinger defaults if available
    if hostinger_creds:
        return default_config
    
    return {}


class SimpleWordPressDeployer:
    """Simple WordPress deployer using SFTP from site_configs.json"""
    
    def __init__(self, site_key: str, site_configs: dict):
        """Initialize deployer with site configuration."""
        self.site_key = site_key
        self.site_config = None
        self.sftp = None
        self.transport = None
        
        # Try to find by site_key directly first (sites.json format)
        if site_key in site_configs:
            self.site_config = site_configs[site_key]
        else:
            # Find site config by domain or site_key (site_configs.json format)
            for domain, config in site_configs.items():
                if site_key in domain or domain.endswith(site_key):
                    self.site_config = config
                    break
        
        if not self.site_config:
            raise ValueError(f"Site '{site_key}' not found in configuration files")
    
    def connect(self) -> bool:
        """Connect to server via SFTP."""
        if not PARAMIKO_AVAILABLE:
            print("❌ paramiko library not installed. Install with: pip install paramiko")
            return False
        
        # Try to get credentials from Hostinger environment variables first
        import os
        from dotenv import load_dotenv
        
        env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
        if env_path.exists():
            load_dotenv(env_path)
        
        # Check environment variables first (Hostinger tool credentials)
        host = os.getenv("HOSTINGER_HOST")
        username = os.getenv("HOSTINGER_USER")
        password = os.getenv("HOSTINGER_PASS")
        port = int(os.getenv("HOSTINGER_PORT", "65002"))
        
        # If env vars not available, try site config
        if not all([host, username, password]):
            # Handle both sites.json and site_configs.json formats
            # sites.json format: direct keys like "host", "username", "password"
            # site_configs.json format: nested under "sftp" key
            if 'sftp' in self.site_config:
                sftp_config = self.site_config.get('sftp', {})
                host = sftp_config.get('host') or host
                username = sftp_config.get('username') or username
                password = sftp_config.get('password') or password
                port = sftp_config.get('port', port or 22)
                remote_path = sftp_config.get('remote_path', '')
            else:
                # sites.json format (direct keys)
                host = self.site_config.get('host') or host
                username = self.site_config.get('username') or username
                password = self.site_config.get('password') or password
                port = self.site_config.get('port', port or 22)
                remote_path = self.site_config.get('remote_path', '')
        else:
            # Use remote_path from site config if available
            if 'sftp' in self.site_config:
                remote_path = self.site_config.get('sftp', {}).get('remote_path', '')
            else:
                remote_path = self.site_config.get('remote_path', '')
        
        if not all([host, username, password]):
            print(f"❌ Incomplete SFTP credentials for {self.site_key}")
            print("   📋 Credential Loading Diagnostics:")
            print(f"      - HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
            print(f"      - HOSTINGER_USER: {'✅ Set' if username else '❌ Missing'}")
            print(f"      - HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
            print(f"      - HOSTINGER_PORT: {port if port else '❌ Missing (default: 65002)'}")
            print("   📋 Configuration Sources Checked:")
            print("      1. Environment variables (.env file)")
            if 'sftp' in self.site_config:
                sftp_config = self.site_config.get('sftp', {})
                print(f"      2. site_configs.json['{self.site_key}']['sftp']")
                print(f"         - host: {'✅ Set' if sftp_config.get('host') else '❌ Missing'}")
                print(f"         - username: {'✅ Set' if sftp_config.get('username') else '❌ Missing'}")
                print(f"         - password: {'✅ Set' if sftp_config.get('password') else '❌ Missing'}")
            else:
                print(f"      2. sites.json['{self.site_key}']")
                print(f"         - host: {'✅ Set' if self.site_config.get('host') else '❌ Missing'}")
                print(f"         - username: {'✅ Set' if self.site_config.get('username') else '❌ Missing'}")
                print(f"         - password: {'✅ Set' if self.site_config.get('password') else '❌ Missing'}")
            print("   💡 Solution: Set HOSTINGER_* environment variables in .env file or add credentials to site config")
            return False
        
        self.remote_path = remote_path
        
        try:
            print(f"🔌 Connecting to {host}:{port} as {username}...")
            self.transport = paramiko.Transport((host, port))
            self.transport.connect(username=username, password=password)
            self.sftp = paramiko.SFTPClient.from_transport(self.transport)
            print(f"✅ Connected successfully to {host}:{port}")
            return True
        except paramiko.AuthenticationException as e:
            print(f"❌ Authentication failed for {self.site_key}")
            print(f"   Error: {str(e)}")
            print(f"   Details: Invalid username or password for {username}@{host}:{port}")
            print("   💡 Solution: Verify credentials in .env file or site config")
            return False
        except paramiko.SSHException as e:
            print(f"❌ SSH connection error for {self.site_key}")
            print(f"   Error: {str(e)}")
            print(f"   Details: Failed to establish SSH connection to {host}:{port}")
            print("   💡 Solution: Check host/port, firewall rules, and network connectivity")
            return False
        except Exception as e:
            print(f"❌ Connection error for {self.site_key}")
            print(f"   Error Type: {type(e).__name__}")
            print(f"   Error Message: {str(e)}")
            print(f"   Connection Details: {username}@{host}:{port}")
            import traceback
            print(f"   Full Traceback:")
            traceback.print_exc()
            return False
    
    def deploy_file(self, local_path: Path, remote_path: str = None) -> bool:
        """Deploy a single file to the server."""
        if not self.sftp:
            print("❌ Not connected. Call connect() first.")
            return False
        
        try:
            # Build remote path
            if remote_path:
                # Use provided remote path (already includes base path)
                full_remote_path = remote_path
            else:
                # Build from base remote_path + local filename
                base_path = getattr(self, 'remote_path', '')
                if base_path:
                    full_remote_path = f"{base_path}/{local_path.name}"
                else:
                    full_remote_path = local_path.name
            
            # Normalize path separators
            full_remote_path = full_remote_path.replace('\\', '/')
            
            # Ensure we're using absolute path from home directory
            # Hostinger structure: /home/username/domains/domain.com/public_html/...
            if not full_remote_path.startswith('/'):
                # If relative, make it absolute from home
                username = self.site_config.get('username') if 'username' in self.site_config else self.site_config.get('sftp', {}).get('username', '')
                if username and not full_remote_path.startswith(f'/home/{username}'):
                    # Prepend home directory if not already there
                    if full_remote_path.startswith('domains/'):
                        full_remote_path = f"/home/{username}/{full_remote_path}"
                    elif full_remote_path.startswith('wp-content/'):
                        # This is wrong - should be in domains/domain.com/public_html/wp-content/
                        base_path = getattr(self, 'remote_path', '')
                        if base_path:
                            full_remote_path = f"/home/{username}/{base_path}/{full_remote_path}"
            
            # Ensure remote directory exists
            remote_dir = str(Path(full_remote_path).parent)
            
            # Create directory recursively using absolute paths
            parts = remote_dir.strip('/').split('/')
            current = ''
            for part in parts:
                if part:
                    current = f"{current}/{part}" if current else f"/{part}"
                    try:
                        self.sftp.stat(current)
                    except FileNotFoundError:
                        try:
                            self.sftp.mkdir(current)
                        except Exception as e:
                            # Directory might already exist or permission issue
                            pass
            
            # Upload file (use absolute path)
            self.sftp.put(str(local_path), full_remote_path)
            return True
        except paramiko.SSHException as e:
            print(f"❌ SFTP upload error for {self.site_key}")
            print(f"   Error Type: SSHException")
            print(f"   Error Message: {str(e)}")
            print(f"   Local File: {local_path}")
            print(f"   Remote Path: {full_remote_path}")
            print("   💡 Solution: Check file permissions, disk space, and remote path validity")
            return False
        except IOError as e:
            print(f"❌ SFTP file I/O error for {self.site_key}")
            print(f"   Error Type: IOError")
            print(f"   Error Message: {str(e)}")
            print(f"   Local File: {local_path}")
            print(f"   Remote Path: {full_remote_path}")
            print("   💡 Solution: Verify local file exists and is readable, check remote directory permissions")
            return False
        except Exception as e:
            print(f"❌ SFTP upload error for {self.site_key}")
            print(f"   Error Type: {type(e).__name__}")
            print(f"   Error Message: {str(e)}")
            print(f"   Local File: {local_path}")
            print(f"   Remote Path: {full_remote_path}")
            import traceback
            print(f"   Full Traceback:")
            traceback.print_exc()
            return False
    
    def execute_command(self, command: str) -> str:
        """Execute a command via SSH using the same credential loading as connect()."""
        if not PARAMIKO_AVAILABLE:
            return ""
        
        try:
            # Use same credential loading logic as connect() method
            import os
            from dotenv import load_dotenv
            
            env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
            if env_path.exists():
                load_dotenv(env_path)
            
            # Check environment variables first (Hostinger tool credentials)
            host = os.getenv("HOSTINGER_HOST")
            username = os.getenv("HOSTINGER_USER")
            password = os.getenv("HOSTINGER_PASS")
            port = int(os.getenv("HOSTINGER_PORT", "65002"))  # Hostinger uses 65002
            
            # If env vars not available, try site config
            if not all([host, username, password]):
                if 'sftp' in self.site_config:
                    sftp_config = self.site_config.get('sftp', {})
                    host = sftp_config.get('host') or host
                    username = sftp_config.get('username') or username
                    password = sftp_config.get('password') or password
                    port = sftp_config.get('port', port or 65002)  # Default to Hostinger port
                else:
                    host = self.site_config.get('host') or host
                    username = self.site_config.get('username') or username
                    password = self.site_config.get('password') or password
                    port = self.site_config.get('port', port or 65002)  # Default to Hostinger port
            
            if not all([host, username, password]):
                print(f"⚠️  Incomplete SSH credentials for {self.site_key}")
                print("   📋 Credential Loading Diagnostics:")
                print(f"      - HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
                print(f"      - HOSTINGER_USER: {'✅ Set' if username else '❌ Missing'}")
                print(f"      - HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
                return ""
            
            ssh = paramiko.SSHClient()
            ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
            ssh.connect(host, port=port, username=username, password=password, timeout=10)
            stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
            
            output = stdout.read().decode('utf-8')
            error = stderr.read().decode('utf-8')
            
            ssh.close()
            
            if error and "error" in error.lower():
                print(f"⚠️  Command warning: {error[:200]}")
            
            return output if output else error
        except paramiko.AuthenticationException as e:
            print(f"❌ SSH authentication failed for {self.site_key}")
            print(f"   Error: {str(e)}")
            print(f"   Details: Invalid username or password for {username}@{host}:{port}")
            return ""
        except paramiko.SSHException as e:
            print(f"❌ SSH connection error for {self.site_key}")
            print(f"   Error: {str(e)}")
            print(f"   Command: {command}")
            print(f"   Connection: {username}@{host}:{port}")
            return ""
        except Exception as e:
            print(f"❌ SSH command error for {self.site_key}")
            print(f"   Error Type: {type(e).__name__}")
            print(f"   Error Message: {str(e)}")
            print(f"   Command: {command}")
            print(f"   Connection: {username}@{host}:{port}")
            import traceback
            traceback.print_exc()
            return ""
    
    def check_php_syntax(self, remote_file_path: str) -> Dict[str, any]:
        """
        Check PHP file syntax and return detailed error information with line numbers.
        
        Args:
            remote_file_path: Path to PHP file on remote server
            
        Returns:
            Dictionary with syntax check results including line numbers
        """
        if not self.sftp:
            return {
                "valid": False,
                "error": "Not connected. Call connect() first.",
                "line_number": None,
                "error_message": None
            }
        
        try:
            command = f"php -l {remote_file_path} 2>&1"
            result = self.execute_command(command)
            
            # Parse PHP syntax error output
            if "No syntax errors" in result or "syntax is OK" in result:
                return {
                    "valid": True,
                    "error": None,
                    "line_number": None,
                    "error_message": None,
                    "output": result.strip()
                }
            
            # Extract line number from error message
            # PHP error format: "Parse error: ... in /path/to/file.php on line N"
            import re
            line_match = re.search(r'on line (\d+)', result, re.IGNORECASE)
            line_number = int(line_match.group(1)) if line_match else None
            
            # Extract error type and message
            error_type_match = re.search(r'(Parse error|Fatal error|Warning|Notice):\s*(.+?)(?:\s+in\s|$)', result, re.IGNORECASE | re.DOTALL)
            error_type = error_type_match.group(1) if error_type_match else "Unknown error"
            error_message = error_type_match.group(2).strip() if error_type_match else result.strip()
            
            # Get context around error line if line number found
            context = None
            if line_number:
                try:
                    # Read lines around the error (5 lines before and after)
                    start_line = max(1, line_number - 5)
                    end_line = line_number + 5
                    context_command = f"sed -n '{start_line},{end_line}p' {remote_file_path}"
                    context = self.execute_command(context_command)
                except Exception:
                    context = None
            
            return {
                "valid": False,
                "error": error_type,
                "line_number": line_number,
                "error_message": error_message,
                "output": result.strip(),
                "context": context,
                "file_path": remote_file_path
            }
        except Exception as e:
            return {
                "valid": False,
                "error": f"Syntax check failed: {str(e)}",
                "line_number": None,
                "error_message": str(e),
                "output": None
            }
    
    def disconnect(self):
        """Disconnect from server."""
        if self.sftp:
            self.sftp.close()
        if self.transport:
            self.transport.close()

