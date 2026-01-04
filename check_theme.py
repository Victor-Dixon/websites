#!/usr/bin/env python3
"""
Check theme files and syntax
"""

import paramiko

def check_theme():
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        # Check theme files
        stdin, stdout, stderr = ssh.exec_command('ls -la domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/')
        theme_files = stdout.read().decode()
        print('Theme files:')
        print(theme_files)

        # Check functions.php for syntax errors
        stdin2, stdout2, stderr2 = ssh.exec_command('php -l domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/functions.php 2>&1 || echo "Syntax check failed"')
        syntax_check = stdout2.read().decode()
        print('\nFunctions.php syntax check:')
        print(syntax_check)

    finally:
        ssh.close()

if __name__ == '__main__':
    check_theme()