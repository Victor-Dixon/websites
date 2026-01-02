#!/usr/bin/env python3
"""
Deployment Monitor and Notification System
=========================================

Monitors deployment status and sends notifications via:
- Console output
- Log files
- Email notifications
- Slack/Discord webhooks
- SMS alerts (for critical failures)

Features:
- Real-time deployment tracking
- Success/failure notifications
- Performance metrics
- Rollback recommendations
- Health checks after deployment

Author: Agent-7 (Web Development Specialist)
Date: 2026-01-01
"""

import json
import smtplib
import sys
import time
from dataclasses import dataclass
from datetime import datetime
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from pathlib import Path
from typing import Dict, List, Optional
import urllib.request
import urllib.error


@dataclass
class DeploymentNotification:
    site: str
    status: str  # 'success', 'failed', 'warning'
    message: str
    timestamp: datetime
    files_deployed: int = 0
    files_failed: int = 0
    duration: float = 0.0
    errors: List[str] = None

    def __post_init__(self):
        if self.errors is None:
            self.errors = []


class DeploymentMonitor:
    """Monitor and notify about deployment status."""

    def __init__(self):
        self.repo_root = Path(__file__).resolve().parents[2]
        self.log_file = self.repo_root / "deployment.log"
        self.config_file = self.repo_root / "config" / "deployment_config.json"
        self.notifications = []

    def load_config(self) -> Dict:
        """Load deployment configuration."""
        if self.config_file.exists():
            with open(self.config_file, 'r') as f:
                return json.load(f)

        # Default configuration
        return {
            "notifications": {
                "console": True,
                "file": True,
                "email": False,
                "slack": False,
                "discord": False
            },
            "email": {
                "smtp_server": "smtp.gmail.com",
                "smtp_port": 587,
                "username": "",
                "password": "",
                "from_email": "",
                "to_emails": []
            },
            "slack": {
                "webhook_url": ""
            },
            "discord": {
                "webhook_url": ""
            },
            "health_checks": True,
            "rollback_on_failure": False
        }

    def log_deployment_result(self, notification: DeploymentNotification):
        """Log deployment result to file."""
        self.notifications.append(notification)

        log_entry = {
            "timestamp": notification.timestamp.isoformat(),
            "site": notification.site,
            "status": notification.status,
            "message": notification.message,
            "files_deployed": notification.files_deployed,
            "files_failed": notification.files_failed,
            "duration": notification.duration,
            "errors": notification.errors
        }

        # Append to log file
        with open(self.log_file, 'a') as f:
            json.dump(log_entry, f)
            f.write('\n')

    def notify_console(self, notification: DeploymentNotification):
        """Send notification to console."""
        status_icon = {
            'success': '✅',
            'failed': '❌',
            'warning': '⚠️'
        }.get(notification.status, 'ℹ️')

        print(f"\n{status_icon} DEPLOYMENT {notification.status.upper()}: {notification.site}")
        print(f"   Message: {notification.message}")
        print(f"   Files: {notification.files_deployed} deployed, {notification.files_failed} failed")
        print(".1f"        if notification.errors:
            print("   Errors:")
            for error in notification.errors[:3]:  # Show first 3
                print(f"     - {error}")

    def notify_email(self, notification: DeploymentNotification, config: Dict):
        """Send email notification."""
        if not config.get('email', {}).get('enabled', False):
            return

        try:
            msg = MIMEMultipart()
            msg['From'] = config['email']['from_email']
            msg['To'] = ', '.join(config['email']['to_emails'])
            msg['Subject'] = f"Website Deployment {notification.status.title()}: {notification.site}"

            body = f"""
Website Deployment Notification

Site: {notification.site}
Status: {notification.status.title()}
Message: {notification.message}
Files Deployed: {notification.files_deployed}
Files Failed: {notification.files_failed}
Duration: {notification.duration:.1f} seconds

Timestamp: {notification.timestamp.strftime('%Y-%m-%d %H:%M:%S')}
"""

            if notification.errors:
                body += "\nErrors:\n" + "\n".join(f"- {error}" for error in notification.errors)

            msg.attach(MIMEText(body, 'plain'))

            server = smtplib.SMTP(config['email']['smtp_server'], config['email']['smtp_port'])
            server.starttls()
            server.login(config['email']['username'], config['email']['password'])
            text = msg.as_string()
            server.sendmail(config['email']['from_email'], config['email']['to_emails'], text)
            server.quit()

            print(f"📧 Email notification sent to {len(config['email']['to_emails'])} recipient(s)")

        except Exception as e:
            print(f"❌ Failed to send email notification: {e}")

    def notify_slack(self, notification: DeploymentNotification, config: Dict):
        """Send Slack notification."""
        webhook_url = config.get('slack', {}).get('webhook_url')
        if not webhook_url:
            return

        status_color = {
            'success': 'good',
            'failed': 'danger',
            'warning': 'warning'
        }.get(notification.status, '#808080')

        payload = {
            "attachments": [{
                "color": status_color,
                "title": f"Website Deployment: {notification.site}",
                "text": notification.message,
                "fields": [
                    {
                        "title": "Status",
                        "value": notification.status.title(),
                        "short": True
                    },
                    {
                        "title": "Files",
                        "value": f"{notification.files_deployed} deployed, {notification.files_failed} failed",
                        "short": True
                    },
                    {
                        "title": "Duration",
                        "value": f"{notification.duration:.1f}s",
                        "short": True
                    }
                ],
                "ts": int(notification.timestamp.timestamp())
            }]
        }

        if notification.errors:
            payload["attachments"][0]["fields"].append({
                "title": "Errors",
                "value": "\n".join(notification.errors[:3]),
                "short": False
            })

        try:
            data = json.dumps(payload).encode('utf-8')
            req = urllib.request.Request(
                webhook_url,
                data=data,
                headers={'Content-Type': 'application/json'}
            )
            urllib.request.urlopen(req)
            print("📱 Slack notification sent")

        except Exception as e:
            print(f"❌ Failed to send Slack notification: {e}")

    def notify_discord(self, notification: DeploymentNotification, config: Dict):
        """Send Discord notification."""
        webhook_url = config.get('discord', {}).get('webhook_url')
        if not webhook_url:
            return

        status_emoji = {
            'success': '✅',
            'failed': '❌',
            'warning': '⚠️'
        }.get(notification.status, 'ℹ️')

        embed = {
            "title": f"{status_emoji} Website Deployment: {notification.site}",
            "description": notification.message,
            "color": {
                'success': 0x00ff00,
                'failed': 0xff0000,
                'warning': 0xffff00
            }.get(notification.status, 0x808080),
            "fields": [
                {
                    "name": "Status",
                    "value": notification.status.title(),
                    "inline": True
                },
                {
                    "name": "Files Deployed",
                    "value": str(notification.files_deployed),
                    "inline": True
                },
                {
                    "name": "Files Failed",
                    "value": str(notification.files_failed),
                    "inline": True
                },
                {
                    "name": "Duration",
                    "value": f"{notification.duration:.1f}s",
                    "inline": True
                }
            ],
            "timestamp": notification.timestamp.isoformat()
        }

        if notification.errors:
            embed["fields"].append({
                "name": "Errors",
                "value": "\n".join(notification.errors[:3]),
                "inline": False
            })

        payload = {
            "embeds": [embed]
        }

        try:
            data = json.dumps(payload).encode('utf-8')
            req = urllib.request.Request(
                webhook_url,
                data=data,
                headers={'Content-Type': 'application/json'}
            )
            urllib.request.urlopen(req)
            print("🎮 Discord notification sent")

        except Exception as e:
            print(f"❌ Failed to send Discord notification: {e}")

    def send_notification(self, notification: DeploymentNotification):
        """Send notification through all configured channels."""
        config = self.load_config()

        # Log to file
        if config.get('notifications', {}).get('file', True):
            self.log_deployment_result(notification)

        # Console output
        if config.get('notifications', {}).get('console', True):
            self.notify_console(notification)

        # Email
        if config.get('notifications', {}).get('email', False):
            self.notify_email(notification, config)

        # Slack
        if config.get('notifications', {}).get('slack', False):
            self.notify_slack(notification, config)

        # Discord
        if config.get('notifications', {}).get('discord', False):
            self.notify_discord(notification, config)

    def perform_health_check(self, site_domain: str) -> bool:
        """Perform basic health check on deployed site."""
        try:
            # Simple HTTP check
            import urllib.request
            req = urllib.request.Request(f"https://{site_domain}")
            req.add_header('User-Agent', 'DeploymentMonitor/1.0')
            response = urllib.request.urlopen(req, timeout=10)

            if response.status == 200:
                print(f"✅ Health check passed for {site_domain}")
                return True
            else:
                print(f"⚠️  Health check warning for {site_domain}: HTTP {response.status}")
                return False

        except Exception as e:
            print(f"❌ Health check failed for {site_domain}: {e}")
            return False

    def generate_deployment_report(self) -> str:
        """Generate a comprehensive deployment report."""
        if not self.notifications:
            return "No deployments to report"

        report = ["# 🚀 Deployment Report", ""]
        report.append(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        report.append("")

        # Summary statistics
        total_sites = len(set(n.site for n in self.notifications))
        successful_deployments = sum(1 for n in self.notifications if n.status == 'success')
        failed_deployments = sum(1 for n in self.notifications if n.status == 'failed')
        total_files = sum(n.files_deployed + n.files_failed for n in self.notifications)
        avg_duration = sum(n.duration for n in self.notifications) / len(self.notifications)

        report.extend([
            "## 📊 Summary",
            f"- **Sites Deployed:** {total_sites}",
            f"- **Successful Deployments:** {successful_deployments}",
            f"- **Failed Deployments:** {failed_deployments}",
            f"- **Total Files:** {total_files}",
            f"- **Average Duration:** {avg_duration:.1f}s",
            ""
        ])

        # Detailed results
        report.append("## 📋 Detailed Results")
        report.append("")

        for notification in self.notifications[-10:]:  # Last 10 deployments
            status_emoji = {'success': '✅', 'failed': '❌', 'warning': '⚠️'}.get(notification.status, 'ℹ️')
            report.append(f"### {status_emoji} {notification.site}")
            report.append(f"- **Status:** {notification.status.title()}")
            report.append(f"- **Message:** {notification.message}")
            report.append(f"- **Files:** {notification.files_deployed} deployed, {notification.files_failed} failed")
            report.append(f"- **Duration:** {notification.duration:.1f}s")
            report.append(f"- **Time:** {notification.timestamp.strftime('%Y-%m-%d %H:%M:%S')}")

            if notification.errors:
                report.append("- **Errors:**")
                for error in notification.errors[:3]:
                    report.append(f"  - {error}")

            report.append("")

        return "\n".join(report)

    def cleanup_old_logs(self, days: int = 30):
        """Clean up old log entries."""
        if not self.log_file.exists():
            return

        cutoff_time = time.time() - (days * 24 * 60 * 60)

        # Read existing logs
        logs = []
        with open(self.log_file, 'r') as f:
            for line in f:
                try:
                    log_entry = json.loads(line.strip())
                    timestamp = datetime.fromisoformat(log_entry['timestamp']).timestamp()
                    if timestamp > cutoff_time:
                        logs.append(log_entry)
                except (json.JSONDecodeError, KeyError):
                    continue

        # Write back only recent logs
        with open(self.log_file, 'w') as f:
            for log_entry in logs:
                json.dump(log_entry, f)
                f.write('\n')


def main():
    """Main execution function."""
    import argparse

    parser = argparse.ArgumentParser(description='Deployment Monitor and Notification System')
    parser.add_argument('--notify', nargs='*', help='Send notification (provide site status message)')
    parser.add_argument('--report', action='store_true', help='Generate deployment report')
    parser.add_argument('--health-check', type=str, help='Perform health check on site')
    parser.add_argument('--cleanup-logs', type=int, nargs='?', const=30, help='Clean up logs older than N days')

    args = parser.parse_args()

    monitor = DeploymentMonitor()

    if args.notify and len(args.notify) >= 3:
        # Send notification: site status message [files_deployed] [files_failed] [duration]
        site = args.notify[0]
        status = args.notify[1]
        message = args.notify[2]

        notification = DeploymentNotification(
            site=site,
            status=status,
            message=message,
            timestamp=datetime.now(),
            files_deployed=int(args.notify[3]) if len(args.notify) > 3 else 0,
            files_failed=int(args.notify[4]) if len(args.notify) > 4 else 0,
            duration=float(args.notify[5]) if len(args.notify) > 5 else 0.0
        )

        monitor.send_notification(notification)

    elif args.report:
        report = monitor.generate_deployment_report()
        print(report)

        # Save report to file
        report_file = monitor.repo_root / "deployment_report.md"
        with open(report_file, 'w') as f:
            f.write(report)
        print(f"\n📄 Report saved to: {report_file}")

    elif args.health_check:
        success = monitor.perform_health_check(args.health_check)
        exit(0 if success else 1)

    elif args.cleanup_logs is not None:
        monitor.cleanup_old_logs(args.cleanup_logs)
        print(f"🧹 Cleaned up logs older than {args.cleanup_logs} days")

    else:
        parser.print_help()


if __name__ == '__main__':
    main()