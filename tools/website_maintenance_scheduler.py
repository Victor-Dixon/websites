#!/usr/bin/env python3
"""
Website Maintenance Scheduler
==============================

Schedules and tracks website maintenance tasks.

Author: Agent-7 (Web Development Specialist)
Date: 2025-11-29
"""

import json
from datetime import datetime, timedelta
from pathlib import Path
from typing import Dict, List


class MaintenanceScheduler:
    """Schedules and tracks website maintenance."""
    
    def __init__(self):
        self.schedule_file = Path(__file__).parent / 'maintenance_schedule.json'
        self.tasks = self._load_schedule()
    
    def _load_schedule(self) -> Dict:
        """Load maintenance schedule from file."""
        if self.schedule_file.exists():
            with open(self.schedule_file, 'r') as f:
                return json.load(f)
        return {
            'daily': [],
            'weekly': [],
            'monthly': [],
            'quarterly': []
        }
    
    def _save_schedule(self):
        """Save maintenance schedule to file."""
        with open(self.schedule_file, 'w') as f:
            json.dump(self.tasks, f, indent=2)
    
    def add_task(self, frequency: str, task: str, site: str = 'all'):
        """Add a maintenance task."""
        if frequency not in self.tasks:
            self.tasks[frequency] = []
        
        self.tasks[frequency].append({
            'task': task,
            'site': site,
            'added': datetime.now().isoformat(),
            'last_run': None,
            'next_run': None
        })
        self._save_schedule()
    
    def get_tasks_due(self) -> List[Dict]:
        """Get tasks that are due to run."""
        due_tasks = []
        now = datetime.now()
        
        for frequency, task_list in self.tasks.items():
            for task in task_list:
                if task.get('next_run'):
                    next_run = datetime.fromisoformat(task['next_run'])
                    if next_run <= now:
                        due_tasks.append({
                            **task,
                            'frequency': frequency
                        })
        
        return due_tasks
    
    def mark_completed(self, task_index: int, frequency: str):
        """Mark a task as completed."""
        task = self.tasks[frequency][task_index]
        task['last_run'] = datetime.now().isoformat()
        
        # Calculate next run
        if frequency == 'daily':
            next_run = datetime.now() + timedelta(days=1)
        elif frequency == 'weekly':
            next_run = datetime.now() + timedelta(weeks=1)
        elif frequency == 'monthly':
            next_run = datetime.now() + timedelta(days=30)
        elif frequency == 'quarterly':
            next_run = datetime.now() + timedelta(days=90)
        else:
            next_run = None
        
        if next_run:
            task['next_run'] = next_run.isoformat()
        
        self._save_schedule()
    
    def print_schedule(self):
        """Print maintenance schedule."""
        print("\n" + "="*60)
        print("📅 WEBSITE MAINTENANCE SCHEDULE")
        print("="*60 + "\n")
        
        for frequency in ['daily', 'weekly', 'monthly', 'quarterly']:
            tasks = self.tasks.get(frequency, [])
            if tasks:
                print(f"📋 {frequency.upper()}:")
                for i, task in enumerate(tasks, 1):
                    status = "✅" if task.get('last_run') else "⏳"
                    print(f"  {status} {i}. [{task['site']}] {task['task']}")
                    if task.get('last_run'):
                        print(f"      Last run: {task['last_run']}")
                    if task.get('next_run'):
                        print(f"      Next run: {task['next_run']}")
                print()
        
        # Show due tasks
        due = self.get_tasks_due()
        if due:
            print("🚨 TASKS DUE:")
            for task in due:
                print(f"  ⚠️  [{task['frequency']}] [{task['site']}] {task['task']}")
            print()


def initialize_default_schedule():
    """Initialize default maintenance schedule."""
    scheduler = MaintenanceScheduler()
    
    # Daily tasks
    scheduler.add_task('daily', 'Check site uptime', 'all')
    scheduler.add_task('daily', 'Check for errors in logs', 'all')
    
    # Weekly tasks
    scheduler.add_task('weekly', 'Run website fixes verification', 'all')
    scheduler.add_task('weekly', 'Check WordPress core version', 'all')
    scheduler.add_task('weekly', 'Check plugin versions', 'all')
    scheduler.add_task('weekly', 'Review security logs', 'all')
    
    # Monthly tasks
    scheduler.add_task('monthly', 'Full security audit', 'all')
    scheduler.add_task('monthly', 'Performance audit', 'all')
    scheduler.add_task('monthly', 'Backup verification', 'all')
    scheduler.add_task('monthly', 'Update WordPress core', 'all')
    scheduler.add_task('monthly', 'Update plugins', 'all')
    
    # Quarterly tasks
    scheduler.add_task('quarterly', 'Full site audit', 'all')
    scheduler.add_task('quarterly', 'Review and update security headers', 'all')
    scheduler.add_task('quarterly', 'Performance optimization review', 'all')
    
    return scheduler


def main():
    """Main execution."""
    scheduler = initialize_default_schedule()
    scheduler.print_schedule()
    
    print("💡 To add a task:")
    print("   scheduler.add_task('weekly', 'Your task', 'site_name')")
    print("\n💡 To mark completed:")
    print("   scheduler.mark_completed(task_index, 'frequency')")


if __name__ == '__main__':
    main()

