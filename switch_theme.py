#!/usr/bin/env python3
"""
Switch to default theme to test if custom theme is causing 500 error
"""

import paramiko

def switch_theme():
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        # Switch to default theme temporarily
        stdin, stdout, stderr = ssh.exec_command('cd domains/freerideinvestor.com/public_html && /usr/local/bin/wp theme activate twentytwentyfour --allow-root 2>&1 || echo "WP-CLI failed"')
        result = stdout.read().decode()
        print('Theme switch result:', result)

    finally:
        ssh.close()

if __name__ == '__main__':
    switch_theme()