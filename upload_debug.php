#!/usr/bin/env python3
import paramiko
from pathlib import Path

# SFTP credentials for freerideinvestor.com
SFTP_CONFIG = {
    'host': '157.173.214.121',
    'username': 'u996867598',
    'password': 'Falcons#1247',
    'port': 65002
}

def upload_and_run_debug():
    try:
        transport = paramiko.Transport((SFTP_CONFIG['host'], SFTP_CONFIG['port']))
        transport.connect(username=SFTP_CONFIG['username'], password=SFTP_CONFIG['password'])
        sftp = paramiko.SFTPClient.from_transport(transport)

        # Upload debug script
        local_file = Path('debug_post_type.php')
        remote_file = 'domains/freerideinvestor.com/public_html/debug_post_type.php'

        print(f'📤 Uploading debug script...')
        sftp.put(str(local_file), remote_file)

        sftp.close()
        transport.close()

        print('✅ Debug script uploaded')

        # Now execute it
        print('🔍 Executing debug script...')
        transport2 = paramiko.Transport((SFTP_CONFIG['host'], SFTP_CONFIG['port']))
        transport2.connect(username=SFTP_CONFIG['username'], password=SFTP_CONFIG['password'])

        # Use PHP to execute the script
        stdin, stdout, stderr = transport2.exec_command('cd domains/freerideinvestor.com/public_html && php debug_post_type.php')

        output = stdout.read().decode()
        error = stderr.read().decode()

        transport2.close()

        print('=== DEBUG OUTPUT ===')
        print(output)
        if error:
            print('=== ERRORS ===')
            print(error)

        return True

    except Exception as e:
        print(f'❌ Debug failed: {e}')
        return False

if __name__ == '__main__':
    upload_and_run_debug()