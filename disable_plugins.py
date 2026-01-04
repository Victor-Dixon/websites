#!/usr/bin/env python3
"""
Disable all plugins to test for plugin conflicts
"""

import paramiko

def disable_plugins():
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        # Disable all plugins
        stdin, stdout, stderr = ssh.exec_command('cd domains/freerideinvestor.com/public_html && /usr/local/bin/wp plugin deactivate --all --allow-root 2>&1 || echo "WP-CLI failed"')
        result = stdout.read().decode()
        print('Plugin deactivation result:', result)

    finally:
        ssh.close()

if __name__ == '__main__':
    disable_plugins()