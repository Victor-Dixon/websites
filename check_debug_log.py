#!/usr/bin/env python3
"""
Check WordPress debug.log for errors
"""

import paramiko

def check_debug_log():
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        # Check for debug.log
        stdin, stdout, stderr = ssh.exec_command('ls -la domains/freerideinvestor.com/public_html/wp-content/debug.log 2>/dev/null || echo "debug.log not found"')
        result = stdout.read().decode().strip()
        print('Debug log check:', result)

        if 'debug.log' in result:
            # Read the last few lines of debug.log
            stdin2, stdout2, stderr2 = ssh.exec_command('tail -20 domains/freerideinvestor.com/public_html/wp-content/debug.log')
            debug_content = stdout2.read().decode()
            print('\nLast 20 lines of debug.log:')
            print(debug_content)
        else:
            print('No debug.log found')

    finally:
        ssh.close()

if __name__ == '__main__':
    check_debug_log()