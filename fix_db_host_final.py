#!/usr/bin/env python3
"""
Fix DB_HOST to localhost for both sites
"""

import paramiko
import tempfile
import os

def fix_db_host():
    # SFTP connection details
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    # Connect
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        sites = ['freerideinvestor.com', 'prismblossom.online']

        for site in sites:
            # Read current wp-config.php
            stdin, stdout, stderr = ssh.exec_command(f'cat domains/{site}/public_html/wp-config.php')
            content = stdout.read().decode()

            # Replace DB_HOST to localhost
            old_line = "define( 'DB_HOST', '157.173.214.121' ); // Fixed database host"
            new_line = "define( 'DB_HOST', 'localhost' ); // Correct for shared hosting"
            new_content = content.replace(old_line, new_line)

            # Write back
            with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.php') as temp_file:
                temp_file.write(new_content)
                temp_file_path = temp_file.name

            sftp = ssh.open_sftp()
            sftp.put(temp_file_path, f'domains/{site}/public_html/wp-config.php')
            sftp.close()
            os.unlink(temp_file_path)

            print(f'✅ Fixed DB_HOST for {site}')

    finally:
        ssh.close()

if __name__ == '__main__':
    fix_db_host()