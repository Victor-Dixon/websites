#!/usr/bin/env python3
"""
Fix freerideinvestor.com empty page issue - CRITICAL PRODUCTION FIX
"""

import paramiko
import tempfile
import os

def fix_freerideinvestor():
    """Fix the critical freerideinvestor.com empty page issue"""

    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print("🔧 Starting CRITICAL PRODUCTION FIX for freerideinvestor.com")

        # Step 1: Switch to default theme to bypass theme issues
        print("🎨 Step 1: Switching to default WordPress theme...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            '/usr/local/bin/wp theme activate twentytwentyfour --allow-root'
        )
        theme_result = stdout.read().decode()
        if "Success" in theme_result:
            print("✅ Theme switched to Twenty Twenty-Four")
        else:
            print(f"⚠️ Theme switch result: {theme_result}")

        # Step 2: Clear all caches
        print("🧹 Step 2: Clearing all WordPress caches...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            '/usr/local/bin/wp cache flush --allow-root'
        )
        cache_result = stdout.read().decode()
        print(f"Cache flush result: {cache_result}")

        # Step 3: Check for any remaining issues
        print("🔍 Step 3: Checking site status...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            'curl -s -o /dev/null -w "%{http_code}" https://freerideinvestor.com/'
        )
        http_code = stdout.read().decode().strip()
        print(f"HTTP Status Code: {http_code}")

        if http_code == "200":
            print("✅ Site is now responding with HTTP 200!")
            return True
        else:
            print(f"⚠️ Site still returning HTTP {http_code}")

            # Step 4: Emergency fallback - disable all plugins
            print("🚨 Step 4: Emergency plugin disable...")
            stdin, stdout, stderr = ssh.exec_command(
                'cd domains/freerideinvestor.com/public_html && '
                '/usr/local/bin/wp plugin deactivate --all --allow-root'
            )
            plugin_result = stdout.read().decode()
            print(f"Plugin deactivation result: {plugin_result}")

            return False

    except Exception as e:
        print(f"❌ Error during fix: {e}")
        return False

    finally:
        ssh.close()

if __name__ == "__main__":
    success = fix_freerideinvestor()
    if success:
        print("\n🎉 freerideinvestor.com CRITICAL FIX COMPLETED - Site should now be accessible!")
    else:
        print("\n⚠️ Additional intervention may be needed. Please check site status.")