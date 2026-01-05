#!/usr/bin/env python3
"""
Check the status of episode import system on digitaldreamscape.site
"""

import paramiko

def check_import_status():
    """Check if the episode import system is running and what the current status is"""

    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print("🔍 Checking episode import system status...")

        # Check for running import processes
        stdin, stdout, stderr = ssh.exec_command('ps aux | grep -i import | grep -v grep')
        import_processes = stdout.read().decode()

        if import_processes.strip():
            print('🔄 Running import processes:')
            print(import_processes)
        else:
            print('❌ No import processes currently running')

        # Check for episode-related files on server
        stdin, stdout, stderr = ssh.exec_command('find domains/digitaldreamscape.site -name "*episode*" -o -name "*EP-*" 2>/dev/null | head -10')
        episode_files = stdout.read().decode()

        if episode_files.strip():
            print('\n📁 Episode-related files found:')
            episode_list = episode_files.strip().split('\n')
            print(f'Found {len(episode_list)} episode files (showing first 10):')
            for file in episode_list[:10]:
                print(f'  - {file}')
        else:
            print('\n❌ No episode files found on server')

        # Check current post count
        stdin, stdout, stderr = ssh.exec_command('cd domains/digitaldreamscape.site/public_html && /usr/local/bin/wp post list --post_status=publish --allow-root 2>/dev/null | wc -l')
        post_count = stdout.read().decode().strip()
        print(f'\n📊 Current published posts: {post_count}')

        # Check for import logs or status files
        stdin, stdout, stderr = ssh.exec_command('find domains/digitaldreamscape.site -name "*import*" -o -name "*episode*import*" -o -name "*batch*" 2>/dev/null | head -5')
        import_files = stdout.read().decode()

        if import_files.strip():
            print('\n📋 Import-related files found:')
            for file in import_files.strip().split('\n'):
                print(f'  - {file}')
        else:
            print('\n❌ No import-related files found')

        # Check for PHP import scripts
        stdin, stdout, stderr = ssh.exec_command('find domains/digitaldreamscape.site -name "*.php" | xargs grep -l "import\|episode\|batch" 2>/dev/null | head -5')
        php_scripts = stdout.read().decode()

        if php_scripts.strip():
            print('\n🐘 PHP scripts with import/episode references:')
            for script in php_scripts.strip().split('\n'):
                print(f'  - {script}')
        else:
            print('\n❌ No PHP import scripts found')

    except Exception as e:
        print(f"❌ Error checking import status: {e}")

    finally:
        ssh.close()

if __name__ == "__main__":
    check_import_status()