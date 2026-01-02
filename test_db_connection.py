#!/usr/bin/env python3
"""
Test Database Connection
=======================

Tests database connectivity with different password possibilities.
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def test_db_password(site_domain, db_name, db_user, password):
    """Test database connection with specific credentials."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        # Create a test PHP script
        test_script = f"""<?php
$link = mysqli_connect('127.0.0.1', '{db_user}', '{password}', '{db_name}');
if (!$link) {{
    echo 'Connection failed: ' . mysqli_connect_error();
}} else {{
    echo 'Connection successful';
    mysqli_close($link);
}}
"""

        # Write test script to server
        remote_script = f"domains/{site_domain}/public_html/test_db.php"
        with open("temp_test_db.php", "w") as f:
            f.write(test_script)

        # Upload and execute
        deployer.deploy_file("temp_test_db.php", remote_script)
        result = deployer.execute_command(f"php {remote_script}")

        # Clean up
        deployer.execute_command(f"rm -f {remote_script}")
        Path("temp_test_db.php").unlink(missing_ok=True)

        deployer.disconnect()

        if "Connection successful" in result:
            print(f"✅ PASSWORD FOUND: {password} works for {site_domain}")
            return password
        else:
            print(f"❌ Password {password} failed: {result.strip()}")
            return False

    except Exception as e:
        print(f"❌ Error testing {site_domain}: {e}")
        return False

def main():
    """Test database connections."""
    print("🔍 TESTING DATABASE CONNECTIONS")
    print("=" * 50)

    # Test passwords found in temp files
    test_passwords = [
        'tCqiZyJgMX',  # prismblossom from temp file
        'tU0I5x8AmH',  # southwestsecret from temp file
        'sUlnVM9fPd',  # ariajet working password
        'Falcons#1247',  # FTP password (probably wrong)
    ]

    # Test freerideinvestor.com
    print("\n🔍 Testing freerideinvestor.com...")
    for password in test_passwords:
        if test_db_password('freerideinvestor.com', 'u996867598_6cbPB', 'u996867598_9dVzt', password):
            break

    # Test prismblossom.online
    print("\n🔍 Testing prismblossom.online...")
    for password in test_passwords:
        if test_db_password('prismblossom.online', 'u996867598_vh2Yg', 'u996867598_KFf6G', password):
            break

if __name__ == "__main__":
    main()