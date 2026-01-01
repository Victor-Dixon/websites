#!/usr/bin/env python3
"""
Hostinger Credentials Setup Tool
================================

Helps you set up Hostinger credentials for deployment.
Creates a .env file with your Hostinger SFTP credentials.

Usage:
    python ops/deployment/setup_hostinger_credentials.py

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-24
"""

import os
from pathlib import Path
from getpass import getpass


def get_hostinger_info():
    """Get Hostinger credentials from user."""
    print("\n" + "="*60)
    print("🔧 HOSTINGER CREDENTIALS SETUP")
    print("="*60)
    print("\nThis tool will help you set up Hostinger SFTP credentials.")
    print("You can find these in your Hostinger hPanel:\n")
    print("  1. Go to: https://hpanel.hostinger.com")
    print("  2. Navigate to: Hosting → FTP Accounts")
    print("  3. Or check: Advanced → SSH Access\n")
    
    host = input("Hostinger SFTP Host (e.g., us-bos-web1616.main-hosting.eu): ").strip()
    username = input("Hostinger Username (e.g., u996867598): ").strip()
    password = getpass("Hostinger Password: ").strip()
    port = input("SFTP Port (default: 65002, press Enter to use default): ").strip() or "65002"
    
    return {
        "HOSTINGER_HOST": host,
        "HOSTINGER_USER": username,
        "HOSTINGER_PASS": password,
        "HOSTINGER_PORT": port
    }


def create_env_file(credentials: dict, env_path: Path):
    """Create or update .env file."""
    env_content = []
    
    # Read existing .env if it exists
    if env_path.exists():
        with open(env_path, 'r') as f:
            existing_lines = f.readlines()
        
        # Update existing Hostinger vars or add new ones
        existing_keys = set()
        for line in existing_lines:
            line = line.strip()
            if line and not line.startswith('#'):
                key = line.split('=')[0].strip()
                existing_keys.add(key)
                if key.startswith('HOSTINGER_'):
                    continue  # Skip old Hostinger vars
                env_content.append(line)
    
    # Add Hostinger credentials
    env_content.append("\n# Hostinger SFTP Credentials")
    env_content.append("# Added by setup_hostinger_credentials.py")
    for key, value in credentials.items():
        env_content.append(f"{key}={value}")
    
    # Write to file
    with open(env_path, 'w') as f:
        f.write('\n'.join(env_content))
        f.write('\n')
    
    print(f"\n✅ Credentials saved to: {env_path}")
    print("   (This file should NOT be committed to git)")


def main():
    """Main execution."""
    # Determine .env file location
    env_paths = [
        Path(".env"),  # Current directory
        Path.home() / ".hostinger_env",  # Home directory
    ]
    
    # Try to find existing .env
    env_path = None
    for path in env_paths:
        if path.exists():
            env_path = path
            break
    
    if not env_path:
        # Use current directory .env
        env_path = Path(".env")
    
    print(f"\n📁 Will save credentials to: {env_path.absolute()}")
    
    # Check if file exists and ask for confirmation
    if env_path.exists():
        response = input("\n⚠️  .env file already exists. Update it? (y/n): ").strip().lower()
        if response != 'y':
            print("❌ Cancelled.")
            return 1
    
    # Get credentials
    credentials = get_hostinger_info()
    
    # Validate
    if not all([credentials["HOSTINGER_HOST"], credentials["HOSTINGER_USER"], credentials["HOSTINGER_PASS"]]):
        print("\n❌ Error: All fields are required!")
        return 1
    
    # Create/update .env file
    create_env_file(credentials, env_path)
    
    # Test connection (optional)
    print("\n" + "="*60)
    print("🧪 TESTING CONNECTION")
    print("="*60)
    
    test = input("\nWould you like to test the connection? (y/n): ").strip().lower()
    if test == 'y':
        try:
            import paramiko
            from dotenv import load_dotenv
            
            load_dotenv(env_path)
            
            host = os.getenv("HOSTINGER_HOST")
            username = os.getenv("HOSTINGER_USER")
            password = os.getenv("HOSTINGER_PASS")
            port = int(os.getenv("HOSTINGER_PORT", "65002"))
            
            print(f"\n🔌 Connecting to {host}:{port} as {username}...")
            transport = paramiko.Transport((host, port))
            transport.connect(username=username, password=password)
            sftp = paramiko.SFTPClient.from_transport(transport)
            
            print("✅ Connection successful!")
            sftp.close()
            transport.close()
            
        except ImportError:
            print("⚠️  paramiko not installed. Install with: pip install paramiko")
            print("   Connection test skipped.")
        except Exception as e:
            print(f"❌ Connection failed: {e}")
            print("   Please check your credentials and try again.")
    
    print("\n" + "="*60)
    print("✅ SETUP COMPLETE")
    print("="*60)
    print("\nYou can now use the deployment tools:")
    print("  python ops/deployment/unified_deployer.py --site digitaldreamscape.site")
    print("  python ops/deployment/unified_deployer.py --all")
    print("\n💡 Tip: Make sure .env is in your .gitignore to keep credentials safe!")
    
    return 0


if __name__ == '__main__':
    exit(main())








