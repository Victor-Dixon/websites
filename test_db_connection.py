#!/usr/bin/env python3
"""
Test database connection directly
"""

import mysql.connector
from mysql.connector import Error

def test_db_connection(host, user, password, database):
    """Test database connection."""
    try:
        print(f"🔍 Testing connection to {database} as {user}@{host}...")

        connection = mysql.connector.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )

        if connection.is_connected():
            db_info = connection.get_server_info()
            print(f"✅ Connected to MySQL Server version {db_info}")

            cursor = connection.cursor()
            cursor.execute("SELECT DATABASE();")
            record = cursor.fetchone()
            print(f"✅ Connected to database: {record[0]}")

            cursor.execute("SHOW TABLES;")
            tables = cursor.fetchall()
            print(f"✅ Found {len(tables)} tables in database")

            return True

    except Error as e:
        print(f"❌ Database connection failed: {e}")
        return False

    finally:
        if 'connection' in locals() and connection.is_connected():
            connection.close()
            print("✅ Database connection closed")

def main():
    """Test database connections."""
    print("🔍 DATABASE CONNECTION TESTING")
    print("=" * 40)

    # Test freerideinvestor.com database
    print("\nFreerideInvestor Database:")
    test_db_connection('127.0.0.1', 'u996867598_9dVzt', 'Falcons#1247', 'u996867598_6cbPB')

    # Test prismblossom.online database
    print("\nPrismBlossom Database:")
    test_db_connection('127.0.0.1', 'u996867598_KFf6G', 'tCqiZyJgMX', 'u996867598_vh2Yg')

if __name__ == "__main__":
    main()