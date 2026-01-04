#!/usr/bin/env python3
"""
Clear WordPress and LiteSpeed cache
"""

import paramiko

def clear_cache():
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        # Clear LiteSpeed cache
        stdin, stdout, stderr = ssh.exec_command('rm -rf domains/freerideinvestor.com/public_html/wp-content/cache/litespeed/* 2>/dev/null; echo "Cache cleared"')
        result = stdout.read().decode()
        print('Cache clear result:', result)

        # Also try purging via WP-CLI if available
        stdin2, stdout2, stderr2 = ssh.exec_command('cd domains/freerideinvestor.com/public_html && /usr/local/bin/wp cache flush 2>/dev/null || echo "WP-CLI not available"')
        wp_cli_result = stdout2.read().decode()
        print('WP-CLI cache flush:', wp_cli_result)

    finally:
        ssh.close()

if __name__ == '__main__':
    clear_cache()