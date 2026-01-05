#!/usr/bin/env python3
"""
Run Canon Declaration System on Digital Dreamscape live server
"""

import paramiko

def run_canon_declaration():
    """Run the canon declaration system scan on the live server"""

    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print("🎭 Running Canon Declaration System on live server...")

        # Change to the website directory
        command = 'cd domains/digitaldreamscape.site/public_html && php canon_declaration_system.php scan'

        stdin, stdout, stderr = ssh.exec_command(command)
        result = stdout.read().decode()
        error = stderr.read().decode()

        if result:
            print("📄 Canon Declaration Output:")
            print(result)

        if error:
            print("⚠️ Errors:")
            print(error)

        print("\n✅ Canon declaration scan completed!")

    except Exception as e:
        print(f"❌ Error running canon declaration: {e}")

    finally:
        ssh.close()

def run_system_status():
    """Run the system status check on the live server"""

    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print("🔍 Checking Digital Dreamscape system status...")

        # Change to the website directory
        command = 'cd domains/digitaldreamscape.site/public_html && php system_status.php'

        stdin, stdout, stderr = ssh.exec_command(command)
        result = stdout.read().decode()
        error = stderr.read().decode()

        if result:
            print("📊 System Status:")
            print(result)

        if error:
            print("⚠️ Errors:")
            print(error)

    except Exception as e:
        print(f"❌ Error checking system status: {e}")

    finally:
        ssh.close()

if __name__ == "__main__":
    import sys

    if len(sys.argv) > 1 and sys.argv[1] == "status":
        run_system_status()
    else:
        run_canon_declaration()