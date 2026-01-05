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

def deploy_files():
    files_to_deploy = [
        ('websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/inc/post-types/free-investor.php', 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/inc/post-types/free-investor.php'),
        ('websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/inc/post-types/cheat-sheet.php', 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/inc/post-types/cheat-sheet.php'),
        ('websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/inc/post-types/tbow-tactics.php', 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/inc/post-types/tbow-tactics.php')
    ]

    deployed = 0
    failed = 0

    try:
        transport = paramiko.Transport((SFTP_CONFIG['host'], SFTP_CONFIG['port']))
        transport.connect(username=SFTP_CONFIG['username'], password=SFTP_CONFIG['password'])
        sftp = paramiko.SFTPClient.from_transport(transport)

        for local_path, remote_path in files_to_deploy:
            local_file = Path(local_path)
            if local_file.exists():
                try:
                    print(f'📤 Deploying {local_path} -> {remote_path}')
                    sftp.put(str(local_file), remote_path)
                    deployed += 1
                except Exception as e:
                    print(f'❌ Failed to deploy {local_path}: {e}')
                    failed += 1
            else:
                print(f'❌ Local file not found: {local_path}')
                failed += 1

        sftp.close()
        transport.close()

        print(f'✅ Deployment complete: {deployed} deployed, {failed} failed')
        return failed == 0

    except Exception as e:
        print(f'❌ Deployment failed: {e}')
        return False

if __name__ == '__main__':
    deploy_files()