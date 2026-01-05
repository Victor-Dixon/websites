#!/usr/bin/env python3
"""
CRITICAL FIX: Replace corrupted index.php with standard WordPress index.php
"""

import paramiko

def fix_index_php():
    """Replace corrupted index.php with standard WordPress index.php"""

    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print("🔧 CRITICAL FIX: Replacing corrupted index.php")

        # Standard WordPress index.php content
        standard_index = '''<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
'''

        # Write the standard index.php
        command = f'cat > domains/freerideinvestor.com/public_html/index.php << \'EOF\'\n{standard_index}\nEOF'
        stdin, stdout, stderr = ssh.exec_command(command)

        print('✅ Replaced corrupted index.php with standard WordPress index.php')

        # Clear any caches
        stdin, stdout, stderr = ssh.exec_command(
            'cd domains/freerideinvestor.com/public_html && /usr/local/bin/wp cache flush --allow-root 2>&1'
        )
        cache_result = stdout.read().decode()
        print(f'Cache flush result: {cache_result.strip()}')

        # Test the fix
        stdin, stdout, stderr = ssh.exec_command(
            'curl -s -o /dev/null -w "%{http_code}" https://freerideinvestor.com/'
        )
        http_code = stdout.read().decode().strip()
        print(f'HTTP Status after fix: {http_code}')

        if http_code == '200':
            print('🎉 SUCCESS: freerideinvestor.com is now working!')
            return True
        else:
            print(f'⚠️ Still issues - HTTP {http_code}')
            return False

    except Exception as e:
        print(f"❌ Error during fix: {e}")
        return False

    finally:
        ssh.close()

if __name__ == "__main__":
    success = fix_index_php()
    if success:
        print("\n✅ CRITICAL PRODUCTION ISSUE RESOLVED")
        print("freerideinvestor.com should now be accessible")
    else:
        print("\n⚠️ Additional fixes may be needed")