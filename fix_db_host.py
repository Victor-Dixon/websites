#!/usr/bin/env python3
"""
Fix DB_HOST for freerideinvestor.com
"""

import sys
sys.path.append('.')
from src.services.wordpress_deployer import WordPressDeployer

def fix_db_host():
    deployer = WordPressDeployer()
    deployer.connect_sftp('freerideinvestor.com')

    # Read current wp-config.php
    stdin, stdout, stderr = deployer.ssh.exec_command('cat domains/freerideinvestor.com/public_html/wp-config.php')
    content = stdout.read().decode()

    # Replace DB_HOST
    old_line = "define( 'DB_HOST', '127.0.0.1' ); // TODO: Replace with actual database host"
    new_line = "define( 'DB_HOST', '157.173.214.121' ); // Fixed database host"
    new_content = content.replace(old_line, new_line)

    # Write back using echo
    command = f"cat > domains/freerideinvestor.com/public_html/wp-config.php << 'EOF'\n{new_content}\nEOF"
    stdin, stdout, stderr = deployer.ssh.exec_command(command)

    print('✅ Updated DB_HOST for freerideinvestor.com')
    deployer.close_connections()

if __name__ == '__main__':
    fix_db_host()