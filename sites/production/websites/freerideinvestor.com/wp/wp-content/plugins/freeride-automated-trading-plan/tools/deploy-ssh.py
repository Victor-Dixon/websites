#!/usr/bin/env python3
"""
SSH Deployment Script for FreeRide Automated Trading Plan Plugin
Deploys plugin files to WordPress server via SSH/SCP

Usage:
    python deploy-ssh.py --action=deploy
    python deploy-ssh.py --action=verify
    python deploy-ssh.py --action=setup

Requirements:
    pip install paramiko scp
"""

import os
import sys
import argparse
import json
from pathlib import Path

try:
    import paramiko
    from scp import SCPClient
except ImportError:
    print("❌ Missing required packages. Install with:")
    print("   pip install paramiko scp")
    sys.exit(1)


class PluginSSHDeployer:
    """Deploy WordPress plugin via SSH"""
    
    def __init__(self, config_file=None):
        self.config = self.load_config(config_file)
        self.plugin_dir = Path(__file__).parent.parent
        self.remote_plugin_path = f"{self.config['remote_base']}/wp-content/plugins/freeride-automated-trading-plan"
        
    def load_config(self, config_file=None):
        """Load SSH configuration"""
        if config_file is None:
            config_file = Path(__file__).parent / 'deploy-config.json'
        
        # Default config (can be overridden by config file)
        default_config = {
            "host": "us-bos-web1616.main-hosting.eu",
            "port": 65002,
            "username": "u996867598",
            "remote_base": "/home/u996867598/public_html",
            "ssh_key_path": None,  # Path to SSH private key
            "ssh_password": None,  # Or use password auth
        }
        
        if config_file.exists():
            with open(config_file, 'r') as f:
                file_config = json.load(f)
                default_config.update(file_config)
        
        return default_config
    
    def get_ssh_client(self):
        """Create and return SSH client"""
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        
        # Try key-based auth first
        if self.config.get('ssh_key_path'):
            key_path = Path(self.config['ssh_key_path']).expanduser()
            if key_path.exists():
                ssh.connect(
                    self.config['host'],
                    port=self.config['port'],
                    username=self.config['username'],
                    key_filename=str(key_path)
                )
                return ssh
        
        # Fallback to password auth
        if self.config.get('ssh_password'):
            ssh.connect(
                self.config['host'],
                port=self.config['port'],
                username=self.config['username'],
                password=self.config['ssh_password']
            )
            return ssh
        
        # Try default SSH key locations
        default_keys = [
            Path.home() / '.ssh' / 'id_rsa',
            Path.home() / '.ssh' / 'id_ed25519',
        ]
        
        for key_path in default_keys:
            if key_path.exists():
                try:
                    ssh.connect(
                        self.config['host'],
                        port=self.config['port'],
                        username=self.config['username'],
                        key_filename=str(key_path)
                    )
                    return ssh
                except:
                    continue
        
        raise Exception("❌ Could not establish SSH connection. Check config or SSH keys.")
    
    def deploy(self):
        """Deploy plugin files to server"""
        print("🚀 Starting plugin deployment...")
        print(f"   Source: {self.plugin_dir}")
        print(f"   Destination: {self.remote_plugin_path}")
        
        ssh = self.get_ssh_client()
        
        try:
            # Create remote directory
            stdin, stdout, stderr = ssh.exec_command(f"mkdir -p {self.remote_plugin_path}")
            stdout.channel.recv_exit_status()
            
            # Deploy files via SCP
            with SCPClient(ssh.get_transport()) as scp:
                print("📦 Uploading plugin files...")
                
                # Get all files to deploy (exclude certain files)
                exclude_patterns = ['.git', '__pycache__', '.pyc', 'node_modules', '.DS_Store']
                files_to_deploy = []
                
                for root, dirs, files in os.walk(self.plugin_dir):
                    # Filter out excluded directories
                    dirs[:] = [d for d in dirs if not any(pattern in d for pattern in exclude_patterns)]
                    
                    for file in files:
                        if not any(pattern in file for pattern in exclude_patterns):
                            file_path = Path(root) / file
                            rel_path = file_path.relative_to(self.plugin_dir)
                            files_to_deploy.append((file_path, rel_path))
                
                # Upload files
                for local_path, rel_path in files_to_deploy:
                    remote_path = f"{self.remote_plugin_path}/{rel_path.as_posix()}"
                    remote_dir = os.path.dirname(remote_path)
                    
                    # Ensure remote directory exists
                    ssh.exec_command(f"mkdir -p {remote_dir}")
                    
                    # Upload file
                    scp.put(str(local_path), remote_path)
                    print(f"   ✓ {rel_path}")
                
                print(f"\n✅ Deployed {len(files_to_deploy)} files successfully!")
                
        except Exception as e:
            print(f"❌ Deployment failed: {e}")
            return False
        finally:
            ssh.close()
        
        return True
    
    def verify(self):
        """Verify plugin installation on server"""
        print("🔍 Verifying plugin installation...")
        
        ssh = self.get_ssh_client()
        
        try:
            checks = {}
            
            # Check main plugin file
            stdin, stdout, stderr = ssh.exec_command(
                f"test -f {self.remote_plugin_path}/freeride-automated-trading-plan.php && echo 'exists' || echo 'missing'"
            )
            main_file = stdout.read().decode().strip()
            checks['Main Plugin File'] = '✅' if main_file == 'exists' else '❌'
            
            # Check required directories
            required_dirs = ['includes', 'templates', 'assets', 'tools']
            for dir_name in required_dirs:
                stdin, stdout, stderr = ssh.exec_command(
                    f"test -d {self.remote_plugin_path}/{dir_name} && echo 'exists' || echo 'missing'"
                )
                dir_status = stdout.read().decode().strip()
                checks[f'Directory: {dir_name}'] = '✅' if dir_status == 'exists' else '❌'
            
            # Check if plugin is active (via WordPress)
            stdin, stdout, stderr = ssh.exec_command(
                f"cd {self.config['remote_base']} && "
                f"wp plugin is-active freeride-automated-trading-plan --allow-root 2>/dev/null && echo 'active' || echo 'inactive'"
            )
            active_status = stdout.read().decode().strip()
            checks['Plugin Active'] = '✅ Active' if active_status == 'active' else '⚠️  Not Active'
            
            # Display results
            print("\n📋 Verification Results:")
            for check, status in checks.items():
                print(f"   {check}: {status}")
            
            all_good = all('✅' in str(status) for status in checks.values())
            return all_good
            
        except Exception as e:
            print(f"❌ Verification failed: {e}")
            return False
        finally:
            ssh.close()
    
    def setup(self):
        """Run WordPress setup commands via SSH"""
        print("⚙️  Running plugin setup...")
        
        ssh = self.get_ssh_client()
        
        try:
            wp_base = self.config['remote_base']
            commands = [
                # Activate plugin
                f"cd {wp_base} && wp plugin activate freeride-automated-trading-plan --allow-root",
                # Create database tables (via setup script)
                f"cd {wp_base} && wp eval-file {self.remote_plugin_path}/SETUP_NOW.php --allow-root",
            ]
            
            for cmd in commands:
                print(f"   Running: {cmd.split('&&')[-1].strip()}")
                stdin, stdout, stderr = ssh.exec_command(cmd)
                exit_status = stdout.channel.recv_exit_status()
                
                if exit_status == 0:
                    output = stdout.read().decode()
                    if output:
                        print(f"   ✓ {output.strip()}")
                else:
                    error = stderr.read().decode()
                    print(f"   ⚠️  {error.strip()}")
            
            print("\n✅ Setup completed!")
            return True
            
        except Exception as e:
            print(f"❌ Setup failed: {e}")
            return False
        finally:
            ssh.close()


def main():
    parser = argparse.ArgumentParser(description='Deploy FreeRide Trading Plan Plugin via SSH')
    parser.add_argument('--action', choices=['deploy', 'verify', 'setup', 'all'], 
                       default='all', help='Action to perform')
    parser.add_argument('--config', help='Path to config file (JSON)')
    
    args = parser.parse_args()
    
    deployer = PluginSSHDeployer(args.config)
    
    if args.action == 'deploy' or args.action == 'all':
        deployer.deploy()
        print()
    
    if args.action == 'verify' or args.action == 'all':
        deployer.verify()
        print()
    
    if args.action == 'setup' or args.action == 'all':
        deployer.setup()


if __name__ == '__main__':
    main()

