#!/usr/bin/env python3
"""
Check PHP syntax of core WordPress files
"""

import paramiko

def check_core_syntax():
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        # Check syntax of main WordPress files
        files_to_check = ['index.php', 'wp-config.php', 'wp-settings.php', 'wp-load.php']

        for file in files_to_check:
            stdin, stdout, stderr = ssh.exec_command(f'cd domains/freerideinvestor.com/public_html && php -l {file} 2>&1 || echo "Syntax check failed for {file}"')
            result = stdout.read().decode()
            print(f'{file} syntax check:')
            print(result)
            print()

    finally:
        ssh.close()

if __name__ == '__main__':
    check_core_syntax()