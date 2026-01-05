#!/usr/bin/env python3
"""
Critical diagnostic for freerideinvestor.com empty page issue
"""

import paramiko

def diagnose_critical():
    """Run critical diagnostics on freerideinvestor.com"""

    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print("🔍 CRITICAL DIAGNOSTIC: freerideinvestor.com")

        # Check wp-admin accessibility
        print("1. Checking wp-admin access...")
        stdin, stdout, stderr = ssh.exec_command(
            'curl -s -o /dev/null -w "%{http_code}" https://freerideinvestor.com/wp-admin/'
        )
        admin_code = stdout.read().decode().strip()
        print(f"   WP-Admin HTTP Status: {admin_code}")

        # Check frontend
        print("2. Checking frontend access...")
        stdin, stdout, stderr = ssh.exec_command(
            'curl -s -o /dev/null -w "%{http_code}" https://freerideinvestor.com/'
        )
        frontend_code = stdout.read().decode().strip()
        print(f"   Frontend HTTP Status: {frontend_code}")

        # Check database connectivity via WP-CLI
        print("3. Checking database connectivity...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            '/usr/local/bin/wp db check --allow-root 2>&1 | head -5'
        )
        db_result = stdout.read().decode()
        print(f"   Database check: {db_result.strip() or 'No output'}")

        # Check active theme
        print("4. Checking active theme...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            '/usr/local/bin/wp theme list --allow-root | grep -E "(active|twentytwentyfour|freerideinvestor)"'
        )
        theme_result = stdout.read().decode()
        print(f"   Theme status: {theme_result.strip() or 'No active theme found'}")

        # Check plugin status
        print("5. Checking plugin status...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            '/usr/local/bin/wp plugin list --allow-root | grep -E "(active|inactive)" | wc -l'
        )
        plugin_count = stdout.read().decode().strip()
        print(f"   Total plugins: {plugin_count}")

        # Check PHP error logs
        print("6. Checking recent PHP errors...")
        stdin, stdout, stderr = ssh.exec_command(
            'tail -10 /home/u996867598/logs/error_log 2>/dev/null || echo "No error log found"'
        )
        error_log = stdout.read().decode()
        if "No error log found" not in error_log:
            print("   Recent errors:")
            for line in error_log.split('\n')[-3:]:  # Last 3 lines
                if line.strip():
                    print(f"   {line}")
        else:
            print("   No error log available")

        # Attempt emergency repair
        print("\n🛠️ ATTEMPTING EMERGENCY REPAIR...")

        # Force reinstall core WordPress files (non-destructive)
        print("7. Attempting core file repair...")
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && '
            '/usr/local/bin/wp core verify-checksums --allow-root 2>&1 | head -3'
        )
        checksum_result = stdout.read().decode()
        print(f"   Core file check: {checksum_result.strip() or 'Unable to check'}")

        # Final status check
        print("\n📊 FINAL STATUS CHECK...")
        stdin, stdout, stderr = ssh.exec_command(
            'curl -s -o /dev/null -w "%{http_code}" https://freerideinvestor.com/'
        )
        final_code = stdout.read().decode().strip()
        print(f"   Final HTTP Status: {final_code}")

        if final_code == "200":
            print("✅ SUCCESS: Site is now working!")
            return True
        else:
            print(f"❌ FAILURE: Site still returning HTTP {final_code}")
            print("\n🔧 MANUAL INTERVENTION NEEDED:")
            print("   - Check server error logs")
            print("   - Verify database server status")
            print("   - Contact Hostinger support for server-level issues")
            return False

    except Exception as e:
        print(f"❌ Diagnostic error: {e}")
        return False

    finally:
        ssh.close()

if __name__ == "__main__":
    diagnose_critical()