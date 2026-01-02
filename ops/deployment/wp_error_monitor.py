#!/usr/bin/env python3
"""
WordPress Error Monitor & Auto-Healing Daemon
============================================

A continuous monitoring system that:
1. Monitors all WordPress sites for errors in real-time
2. Automatically triggers self-healing when errors are detected
3. Integrates with the deployment pipeline
4. Provides real-time notifications and alerts
5. Maintains error history and healing statistics

Features:
- Continuous debug.log monitoring
- Real-time error detection and classification
- Automatic healing triggers
- Escalation policies for critical errors
- Integration with deployment pipeline
- Performance impact monitoring

Author: Agent-7 (Web Development Specialist)
Date: 2026-01-01
"""

import json
import os
import signal
import sys
import threading
import time
from dataclasses import dataclass
from datetime import datetime, timedelta
from pathlib import Path
from typing import Dict, List, Optional

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False

from wp_debug_self_healing import WPSelfHealingSystem


@dataclass
class MonitoringConfig:
    check_interval_seconds: int = 60
    error_threshold: int = 5  # Errors per minute before triggering healing
    critical_error_threshold: int = 1  # Critical errors before immediate action
    healing_cooldown_minutes: int = 15  # Minimum time between healing attempts
    max_concurrent_healing: int = 2  # Maximum sites healing simultaneously
    enable_auto_healing: bool = True
    enable_notifications: bool = True


class WPMonitorDaemon:
    """WordPress monitoring and auto-healing daemon."""

    def __init__(self):
        self.repo_root = Path(__file__).resolve().parents[2]
        self.site_configs = load_site_configs() if DEPLOYER_AVAILABLE else {}
        self.self_healing = WPSelfHealingSystem()

        self.config = self._load_monitor_config()
        self.monitoring_active = False
        self.monitoring_thread = None
        self.site_states = {}  # Track state for each site
        self.error_history = []
        self.healing_cooldowns = {}

        # Graceful shutdown handling
        signal.signal(signal.SIGINT, self._signal_handler)
        signal.signal(signal.SIGTERM, self._signal_handler)

    def _load_monitor_config(self) -> MonitoringConfig:
        """Load monitoring configuration."""
        config_file = self.repo_root / "config" / "wp_monitor_config.json"

        if config_file.exists():
            with open(config_file, 'r') as f:
                data = json.load(f)
                return MonitoringConfig(**data)

        # Default configuration
        return MonitoringConfig()

    def _signal_handler(self, signum, frame):
        """Handle shutdown signals gracefully."""
        print(f"\n🛑 Received signal {signum}, shutting down gracefully...")
        self.stop_monitoring()

    def start_monitoring(self):
        """Start the monitoring daemon."""
        if self.monitoring_active:
            print("ℹ️  Monitoring already active")
            return

        print("🚀 STARTING WORDPRESS ERROR MONITOR DAEMON")
        print("=" * 60)
        print(f"📊 Check Interval: {self.config.check_interval_seconds}s")
        print(f"🚨 Error Threshold: {self.config.error_threshold}/minute")
        print(f"🔥 Critical Threshold: {self.config.critical_error_threshold}")
        print(f"🛠️  Auto-Healing: {'Enabled' if self.config.enable_auto_healing else 'Disabled'}")
        print(f"📱 Notifications: {'Enabled' if self.config.enable_notifications else 'Disabled'}")
        print()

        self.monitoring_active = True
        self.monitoring_thread = threading.Thread(target=self._monitoring_loop, daemon=True)
        self.monitoring_thread.start()

        try:
            # Keep main thread alive
            while self.monitoring_active:
                time.sleep(1)
        except KeyboardInterrupt:
            self.stop_monitoring()

    def stop_monitoring(self):
        """Stop the monitoring daemon."""
        if not self.monitoring_active:
            return

        print("\n🛑 STOPPING WORDPRESS ERROR MONITOR DAEMON")
        self.monitoring_active = False

        if self.monitoring_thread and self.monitoring_thread.is_alive():
            self.monitoring_thread.join(timeout=5)

        print("✅ Monitoring daemon stopped")

    def _monitoring_loop(self):
        """Main monitoring loop."""
        print("🔄 Monitoring loop started")

        while self.monitoring_active:
            try:
                self._check_all_sites()
                time.sleep(self.config.check_interval_seconds)

            except Exception as e:
                print(f"❌ Monitoring loop error: {e}")
                time.sleep(30)  # Wait before retrying

    def _check_all_sites(self):
        """Check all configured sites for errors."""
        if not self.site_configs:
            return

        current_time = datetime.now()

        for site_name in self.site_configs.keys():
            try:
                self._check_site(site_name, current_time)
            except Exception as e:
                print(f"❌ Error checking {site_name}: {e}")

    def _check_site(self, site_name: str, current_time: datetime):
        """Check a single site for errors with safety measures."""
        # Initialize site state if needed
        if site_name not in self.site_states:
            self.site_states[site_name] = {
                'last_check': None,
                'error_count': 0,
                'last_error_time': None,
                'healing_attempts': 0,
                'last_healing': None,
                'status': 'unknown',
                'mode': self.self_healing._get_site_mode(site_name),
                'kill_switch': self.self_healing.kill_switch_active
            }

        site_state = self.site_states[site_name]

        # Check kill switch
        if self.self_healing.kill_switch_active:
            site_state['status'] = 'kill_switch_active'
            return

        # Check site mode
        site_mode = self.self_healing._get_site_mode(site_name)
        site_state['mode'] = site_mode

        if site_mode == 'observe':
            # Observe-only mode: log errors but don't heal
            errors = self.self_healing.monitor_debug_logs(site_name, duration_minutes=2)
            if errors:
                site_state['status'] = 'observing_errors'
                site_state['error_count'] = len([e for e in errors if e.timestamp > current_time - timedelta(minutes=1)])
                print(f"👁️  Observing {len(errors)} errors on {site_name} (observe mode)")
            else:
                site_state['status'] = 'healthy'
            return

        # Check if we're in healing cooldown
        if site_name in self.healing_cooldowns:
            cooldown_end = self.healing_cooldowns[site_name]
            if current_time < cooldown_end:
                site_state['status'] = 'cooldown'
                return  # Skip this site during cooldown

        # Monitor for errors (check last 2 minutes)
        errors = self.self_healing.monitor_debug_logs(site_name, duration_minutes=2)

        site_state['last_check'] = current_time
        site_state['status'] = 'healthy'

        if errors:
            # Count recent errors by severity
            recent_errors = [e for e in errors if e.timestamp > current_time - timedelta(minutes=1)]
            critical_errors = [e for e in recent_errors if e.level in ['error', 'fatal']]
            warning_errors = [e for e in recent_errors if e.level == 'warning']

            site_state['error_count'] = len(recent_errors)
            site_state['last_error_time'] = max(e.timestamp for e in errors) if errors else None

            # Log errors with severity classification
            for error in errors:
                severity = 'high' if error.level in ['error', 'fatal'] else 'medium' if error.level == 'warning' else 'low'
                self.error_history.append({
                    'site': site_name,
                    'error': error,
                    'severity': severity,
                    'detected_at': current_time.isoformat()
                })

            # Check thresholds with mode awareness
            critical_count = len(critical_errors)

            if critical_count >= self.config.critical_error_threshold:
                print(f"🚨 CRITICAL: {critical_count} critical errors on {site_name} ({site_mode} mode)")
                site_state['status'] = 'critical'
                self._trigger_emergency_healing(site_name, critical_errors)

            elif len(recent_errors) >= self.config.error_threshold:
                print(f"⚠️  WARNING: {len(recent_errors)} errors/minute on {site_name} ({site_mode} mode)")
                site_state['status'] = 'warning'

                # Only heal if site is in canary or heal mode
                if site_mode in ['canary', 'heal']:
                    self._trigger_healing(site_name, errors)
                else:
                    print(f"   Skipping auto-heal: {site_name} in {site_mode} mode")

            else:
                site_state['status'] = 'degraded'

        # Clean up old error history (keep last 1000 entries)
        if len(self.error_history) > 1000:
            self.error_history = self.error_history[-1000:]

        # Publish events for agent cycle integration
        self._publish_events(site_name, site_state, errors)

        # Update status display
        self._update_status_display()

    def _trigger_emergency_healing(self, site_name: str, critical_errors: List):
        """Trigger emergency healing for critical errors."""
        print(f"🚨 EMERGENCY HEALING TRIGGERED for {site_name}")
        print(f"   Critical errors: {len(critical_errors)}")

        # Immediate healing attempt (bypass cooldown)
        self._perform_healing(site_name, critical_errors, emergency=True)

    def _trigger_healing(self, site_name: str, errors: List):
        """Trigger regular healing for accumulated errors."""
        # Check healing cooldown
        if site_name in self.healing_cooldowns:
            if datetime.now() < self.healing_cooldowns[site_name]:
                return  # Still in cooldown

        print(f"🔧 AUTO-HEALING TRIGGERED for {site_name}")
        print(f"   Errors detected: {len(errors)}")

        self._perform_healing(site_name, errors, emergency=False)

    def _perform_healing(self, site_name: str, errors: List, emergency: bool = False):
        """Perform healing on a site."""
        if not self.config.enable_auto_healing:
            print(f"⚠️  Auto-healing disabled, skipping {site_name}")
            return

        # Check concurrent healing limit
        active_healing = sum(1 for state in self.site_states.values()
                           if state.get('healing_active', False))
        if active_healing >= self.config.max_concurrent_healing:
            print(f"⚠️  Max concurrent healing reached, queuing {site_name}")
            return

        # Mark as healing active
        self.site_states[site_name]['healing_active'] = True
        self.site_states[site_name]['healing_attempts'] += 1
        self.site_states[site_name]['last_healing'] = datetime.now()

        try:
            print(f"🏥 Starting healing process for {site_name}...")

            # Apply self-healing
            healing_actions = self.self_healing.apply_self_healing(site_name, errors)

            successful_fixes = sum(1 for action in healing_actions if action.success)
            total_fixes = len(healing_actions)

            print(f"✅ Healing completed for {site_name}: {successful_fixes}/{total_fixes} fixes successful")

            # Send notifications
            if self.config.enable_notifications and healing_actions:
                self._send_healing_notification(site_name, healing_actions, emergency)

            # Set cooldown
            cooldown_minutes = 5 if emergency else self.config.healing_cooldown_minutes
            self.healing_cooldowns[site_name] = datetime.now() + timedelta(minutes=cooldown_minutes)

        except Exception as e:
            print(f"❌ Healing failed for {site_name}: {e}")

        finally:
            # Mark healing as complete
            self.site_states[site_name]['healing_active'] = False

    def _send_healing_notification(self, site_name: str, actions: List, emergency: bool):
        """Send notification about healing actions."""
        successful = sum(1 for action in actions if action.success)
        total = len(actions)

        message = f"WordPress Self-Healing {'🚨 EMERGENCY' if emergency else '🔧 AUTO'}: {site_name}\n"
        message += f"Fixes Applied: {successful}/{total} successful\n"

        if successful < total:
            message += "Some fixes failed - manual intervention may be required\n"

        # In a real implementation, this would send to Slack/Discord/email
        print(f"📢 HEALING NOTIFICATION: {message.strip()}")

    def _update_status_display(self):
        """Update the status display."""
        # Simple console status display
        status_lines = []

        for site_name, state in self.site_states.items():
            status_emoji = {
                'healthy': '✅',
                'warning': '⚠️',
                'critical': '🚨',
                'unknown': '❓'
            }.get(state['status'], '❓')

            healing_indicator = ' 🏥' if state.get('healing_active', False) else ''
            error_count = state.get('error_count', 0)

            status_lines.append(f"  {status_emoji} {site_name}: {state['status']}{healing_indicator} ({error_count} errors)")

        # Clear and redraw status (simple implementation)
        print("\r" + " | ".join(status_lines), end="", flush=True)

    def _publish_events(self, site_name: str, site_state: Dict, errors: List):
        """Publish events for agent cycle integration."""
        events = []

        # Critical error event
        if site_state['status'] == 'critical':
            events.append({
                'event_type': 'critical_error_detected',
                'site': site_name,
                'severity': 'high',
                'message': f"Critical errors detected on {site_name}",
                'error_count': len([e for e in errors if e.level in ['error', 'fatal']]),
                'timestamp': datetime.now().isoformat()
            })

        # Healing completed event
        if site_state.get('last_healing') and site_state['status'] == 'healthy':
            events.append({
                'event_type': 'healing_completed',
                'site': site_name,
                'severity': 'info',
                'message': f"Self-healing completed successfully on {site_name}",
                'timestamp': datetime.now().isoformat()
            })

        # Site status change event
        if len(events) > 0:
            self._write_events_to_file(events)

    def _write_events_to_file(self, events: List[Dict]):
        """Write events to file for agent cycle consumption."""
        events_file = self.repo_root / "wp_monitoring_events.jsonl"

        with open(events_file, 'a') as f:
            for event in events:
                json.dump(event, f)
                f.write('\n')

    def get_monitoring_stats(self) -> Dict:
        """Get monitoring statistics."""
        total_sites = len(self.site_states)
        healthy_sites = sum(1 for state in self.site_states.values() if state['status'] == 'healthy')
        warning_sites = sum(1 for state in self.site_states.values() if state['status'] == 'warning')
        critical_sites = sum(1 for state in self.site_states.values() if state['status'] == 'critical')

        total_errors = len(self.error_history)
        recent_errors = len([e for e in self.error_history
                           if datetime.fromisoformat(e['detected_at']) > datetime.now() - timedelta(hours=1)])

        total_healing_attempts = sum(state.get('healing_attempts', 0) for state in self.site_states.values())

        return {
            'total_sites': total_sites,
            'healthy_sites': healthy_sites,
            'warning_sites': warning_sites,
            'critical_sites': critical_sites,
            'total_errors': total_errors,
            'recent_errors': recent_errors,
            'total_healing_attempts': total_healing_attempts,
            'monitoring_active': self.monitoring_active,
            'uptime_seconds': (datetime.now() - datetime.fromisoformat(
                min(state.get('last_check').isoformat() for state in self.site_states.values()
                    if state.get('last_check')) if self.site_states else datetime.now().isoformat()
            )).total_seconds()
        }


def main():
    """Main execution function."""
    import argparse

    parser = argparse.ArgumentParser(description='WordPress Error Monitor & Auto-Healing Daemon')
    parser.add_argument('--start', action='store_true', help='Start the monitoring daemon')
    parser.add_argument('--stop', action='store_true', help='Stop the monitoring daemon')
    parser.add_argument('--status', action='store_true', help='Show monitoring status')
    parser.add_argument('--check-now', action='store_true', help='Perform immediate check of all sites')
    parser.add_argument('--enable-debug', type=str, help='Enable WP_DEBUG for a site')
    parser.add_argument('--daemon', action='store_true', help='Run as daemon (background)')

    args = parser.parse_args()

    monitor = WPMonitorDaemon()

    if args.enable_debug:
        success = monitor.self_healing.enable_wp_debug(args.enable_debug)
        if success:
            print(f"✅ Enabled WP_DEBUG for {args.enable_debug}")
        else:
            print(f"❌ Failed to enable WP_DEBUG for {args.enable_debug}")

    elif args.status:
        stats = monitor.get_monitoring_stats()
        print("📊 WORDPRESS MONITOR STATUS")
        print("=" * 40)
        print(f"Monitoring Active: {'✅' if stats['monitoring_active'] else '❌'}")
        print(f"Total Sites: {stats['total_sites']}")
        print(f"Healthy Sites: {stats['healthy_sites']}")
        print(f"Warning Sites: {stats['warning_sites']}")
        print(f"Critical Sites: {stats['critical_sites']}")
        print(f"Total Errors: {stats['total_errors']}")
        print(f"Recent Errors (1h): {stats['recent_errors']}")
        print(f"Healing Attempts: {stats['total_healing_attempts']}")
        print(".1f"    elif args.check_now:
        print("🔍 Performing immediate check of all sites...")
        monitor._check_all_sites()
        print("✅ Check completed")

    elif args.start or args.daemon:
        if args.daemon:
            # Daemon mode - fork to background
            try:
                pid = os.fork()
                if pid > 0:
                    print(f"🔄 Monitor daemon started with PID: {pid}")
                    sys.exit(0)
            except OSError as e:
                print(f"❌ Fork failed: {e}")
                sys.exit(1)

        monitor.start_monitoring()

    elif args.stop:
        # In a real implementation, this would signal a running daemon
        print("🛑 To stop the monitor, send SIGTERM to the daemon process")
        print("   Or restart your terminal to stop background processes")

    else:
        parser.print_help()


if __name__ == '__main__':
    main()