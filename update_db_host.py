#!/usr/bin/env python3
"""
Update DB_HOST for freerideinvestor.com
"""

import paramiko

def update_db_host():
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
        # Read current wp-config.php
        stdin, stdout, stderr = ssh.exec_command('cat domains/freerideinvestor.com/public_html/wp-config.php')
        content = stdout.read().decode()

        # Replace DB_HOST - for shared hosting, database is usually on localhost from server perspective
        old_line = "define( 'DB_HOST', '127.0.0.1' ); // TODO: Replace with actual database host"
        new_line = "define( 'DB_HOST', 'localhost' ); // Fixed for shared hosting"
        new_content = content.replace(old_line, new_line)

        # Write back using printf to avoid here-doc issues
        import tempfile
        import os

        # Create a temporary file with the content
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.php') as temp_file:
            temp_file.write(new_content)
            temp_file_path = temp_file.name

        # Upload the file for freerideinvestor.com
        sftp = ssh.open_sftp()
        sftp.put(temp_file_path, 'domains/freerideinvestor.com/public_html/wp-config.php')
        sftp.close()

        # Clean up temp file
        os.unlink(temp_file_path)

        print('✅ Updated DB_HOST for freerideinvestor.com')

        # Now do prismblossom.online
        stdin2, stdout2, stderr2 = ssh.exec_command('cat domains/prismblossom.online/public_html/wp-config.php')
        content2 = stdout2.read().decode()

        # Replace DB_HOST for prismblossom
        new_content2 = content2.replace(old_line, new_line)

        # Create temp file for prismblossom
        with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.php') as temp_file2:
            temp_file2.write(new_content2)
            temp_file_path2 = temp_file2.name

        # Upload for prismblossom
        sftp2 = ssh.open_sftp()
        sftp2.put(temp_file_path2, 'domains/prismblossom.online/public_html/wp-config.php')
        sftp2.close()

        # Clean up temp file
        os.unlink(temp_file_path2)

        print('✅ Updated DB_HOST for prismblossom.online')

    finally:
        # Close connection
        ssh.close()

if __name__ == '__main__':
    update_db_host()