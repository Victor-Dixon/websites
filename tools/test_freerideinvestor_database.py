#!/usr/bin/env python3
"""
Test freerideinvestor.com Database Connection
=============================================

Tests database connectivity to identify if database is the issue.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def test_database_connection():
    """Test database connection via wp-config.php credentials."""
    print("=" * 70)
    print("🔍 TESTING FREERIDEINVESTOR.COM DATABASE CONNECTION")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return 1
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return 1
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        
        # Test database connection using wp-cli
        print("1️⃣  Testing database connection via WP-CLI...")
        command = f"cd {remote_path} && wp db check --allow-root 2>&1 || echo 'WP_CLI_NOT_AVAILABLE'"
        result = deployer.execute_command(command)
        
        if "WP_CLI_NOT_AVAILABLE" in result:
            print("   ⚠️  WP-CLI not available, trying alternative method...")
            
            # Try PHP test script
            test_script = f"""<?php
require_once '{remote_path}/wp-config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {{
    echo "CONNECTION_ERROR: " . $conn->connect_error;
}} else {{
    echo "CONNECTION_SUCCESS";
    $conn->close();
}}
"""
            # Write test script
            test_file = f"{remote_path}/db_test.php"
            # Use SFTP to write file
            import tempfile
            with tempfile.NamedTemporaryFile(mode='w', suffix='.php', delete=False) as f:
                f.write(test_script)
                temp_path = Path(f.name)
            
            deployer.deploy_file(temp_path, test_file)
            temp_path.unlink()
            
            # Execute test
            command = f"php {test_file} 2>&1"
            result = deployer.execute_command(command)
            
            # Clean up
            deployer.execute_command(f"rm -f {test_file}")
            
            if "CONNECTION_SUCCESS" in result:
                print("   ✅ Database connection successful")
                return 0
            elif "CONNECTION_ERROR" in result:
                print(f"   ❌ Database connection failed: {result}")
                return 1
            else:
                print(f"   ⚠️  Unexpected result: {result[:200]}")
                return 1
        else:
            if "Success" in result or "OK" in result:
                print("   ✅ Database connection successful")
                return 0
            else:
                print(f"   ⚠️  Database check result: {result[:200]}")
                return 1
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(test_database_connection())


