#!/usr/bin/env python3
"""
Guestbook Automation Script
===========================

Python-based automation for managing WordPress guestbook entries.
Can be used for bulk operations, notifications, or scheduled tasks.

Author: Agent-7 (Web Development Specialist)
License: MIT
"""

import mysql.connector
from datetime import datetime
from typing import List, Dict, Optional
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()


class GuestbookAutomation:
    """Automation class for guestbook management."""
    
    def __init__(self):
        """Initialize database connection."""
        self.db_config = {
            'host': os.getenv('WP_DB_HOST', 'localhost'),
            'user': os.getenv('WP_DB_USER', 'root'),
            'password': os.getenv('WP_DB_PASSWORD', ''),
            'database': os.getenv('WP_DB_NAME', 'wordpress'),
            'charset': 'utf8mb4'
        }
        self.connection = None
    
    def connect(self):
        """Connect to WordPress database."""
        try:
            self.connection = mysql.connector.connect(**self.db_config)
            return True
        except mysql.connector.Error as e:
            print(f"Database connection error: {e}")
            return False
    
    def disconnect(self):
        """Close database connection."""
        if self.connection and self.connection.is_connected():
            self.connection.close()
    
    def get_pending_entries(self) -> List[Dict]:
        """Get all pending guestbook entries."""
        if not self.connection:
            return []
        
        cursor = self.connection.cursor(dictionary=True)
        query = "SELECT * FROM wp_guestbook_entries WHERE status = 'pending' ORDER BY created_at DESC"
        cursor.execute(query)
        entries = cursor.fetchall()
        cursor.close()
        return entries
    
    def approve_entry(self, entry_id: int) -> bool:
        """Approve a guestbook entry."""
        if not self.connection:
            return False
        
        cursor = self.connection.cursor()
        query = "UPDATE wp_guestbook_entries SET status = 'approved' WHERE id = %s"
        cursor.execute(query, (entry_id,))
        self.connection.commit()
        cursor.close()
        return cursor.rowcount > 0
    
    def reject_entry(self, entry_id: int) -> bool:
        """Reject a guestbook entry."""
        if not self.connection:
            return False
        
        cursor = self.connection.cursor()
        query = "UPDATE wp_guestbook_entries SET status = 'rejected' WHERE id = %s"
        cursor.execute(query, (entry_id,))
        self.connection.commit()
        cursor.close()
        return cursor.rowcount > 0
    
    def get_approved_entries(self, limit: int = 50) -> List[Dict]:
        """Get approved guestbook entries."""
        if not self.connection:
            return []
        
        cursor = self.connection.cursor(dictionary=True)
        query = "SELECT * FROM wp_guestbook_entries WHERE status = 'approved' ORDER BY created_at DESC LIMIT %s"
        cursor.execute(query, (limit,))
        entries = cursor.fetchall()
        cursor.close()
        return entries
    
    def get_statistics(self) -> Dict:
        """Get guestbook statistics."""
        if not self.connection:
            return {}
        
        cursor = self.connection.cursor(dictionary=True)
        
        # Total entries
        cursor.execute("SELECT COUNT(*) as total FROM wp_guestbook_entries")
        total = cursor.fetchone()['total']
        
        # Pending entries
        cursor.execute("SELECT COUNT(*) as pending FROM wp_guestbook_entries WHERE status = 'pending'")
        pending = cursor.fetchone()['pending']
        
        # Approved entries
        cursor.execute("SELECT COUNT(*) as approved FROM wp_guestbook_entries WHERE status = 'approved'")
        approved = cursor.fetchone()['approved']
        
        cursor.close()
        
        return {
            'total': total,
            'pending': pending,
            'approved': approved,
            'rejected': total - pending - approved
        }
    
    def auto_approve_recent(self, hours: int = 24) -> int:
        """Auto-approve entries from the last N hours (optional automation)."""
        if not self.connection:
            return 0
        
        cursor = self.connection.cursor()
        query = """
            UPDATE wp_guestbook_entries 
            SET status = 'approved' 
            WHERE status = 'pending' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL %s HOUR)
        """
        cursor.execute(query, (hours,))
        self.connection.commit()
        count = cursor.rowcount
        cursor.close()
        return count


def main():
    """Main function for command-line usage."""
    import sys
    
    automation = GuestbookAutomation()
    
    if not automation.connect():
        print("Failed to connect to database. Check your configuration.")
        sys.exit(1)
    
    try:
        if len(sys.argv) > 1:
            command = sys.argv[1]
            
            if command == 'stats':
                stats = automation.get_statistics()
                print(f"Guestbook Statistics:")
                print(f"  Total: {stats['total']}")
                print(f"  Pending: {stats['pending']}")
                print(f"  Approved: {stats['approved']}")
                print(f"  Rejected: {stats['rejected']}")
            
            elif command == 'pending':
                entries = automation.get_pending_entries()
                print(f"Pending Entries: {len(entries)}")
                for entry in entries:
                    print(f"  [{entry['id']}] {entry['guest_name']}: {entry['message'][:50]}...")
            
            elif command == 'approve' and len(sys.argv) > 2:
                entry_id = int(sys.argv[2])
                if automation.approve_entry(entry_id):
                    print(f"Entry {entry_id} approved successfully.")
                else:
                    print(f"Failed to approve entry {entry_id}.")
            
            elif command == 'auto-approve':
                hours = int(sys.argv[2]) if len(sys.argv) > 2 else 24
                count = automation.auto_approve_recent(hours)
                print(f"Auto-approved {count} entries from the last {hours} hours.")
            
            else:
                print("Usage:")
                print("  python guestbook_automation.py stats")
                print("  python guestbook_automation.py pending")
                print("  python guestbook_automation.py approve <entry_id>")
                print("  python guestbook_automation.py auto-approve [hours]")
        else:
            stats = automation.get_statistics()
            print(f"Guestbook has {stats['total']} total entries ({stats['pending']} pending)")
    
    finally:
        automation.disconnect()


if __name__ == '__main__':
    main()

