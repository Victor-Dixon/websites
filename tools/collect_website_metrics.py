#!/usr/bin/env python3
"""
Website Metrics Collection Tool
===============================

Automated collection and aggregation of website metrics from multiple sources:
- GA4 API (sessions, users, page views, events)
- Form submissions (lead magnets, contact forms)
- Payment systems (bookings, deposits, revenue)
- Custom event tracking

Generates weekly metrics reports for all websites.
"""

import json
import os
from pathlib import Path
from typing import Dict, List, Optional
from datetime import datetime, timedelta
from dataclasses import dataclass, asdict

# Website configuration
WEBSITES = [
    "crosbyultimateevents.com",
    "dadudekc.com",
    "freerideinvestor.com",
    "houstonsipqueen.com",
    "tradingrobotplug.com",
]


@dataclass
class WeeklyMetrics:
    """Weekly metrics data structure."""
    week_starting: str
    domain: str
    sessions: int = 0
    users: int = 0
    new_users: int = 0
    lead_magnet_views: int = 0
    lead_magnet_submits: int = 0
    contact_submits: int = 0
    booking_clicks: int = 0
    bookings: int = 0
    deposits_paid: int = 0
    revenue: float = 0.0
    notes: str = ""


class MetricsCollector:
    """Collects metrics from various sources."""
    
    def __init__(self, project_root: Path):
        self.project_root = project_root
        self.metrics_dir = project_root / "docs" / "metrics"
        self.metrics_dir.mkdir(parents=True, exist_ok=True)
    
    def collect_ga4_metrics(self, domain: str, start_date: str, end_date: str) -> Dict:
        """
        Collect GA4 metrics via API.
        Note: Requires GA4 API credentials and property ID.
        This is a placeholder structure - actual implementation requires:
        - Google Analytics Data API setup
        - OAuth2 authentication
        - Property ID configuration
        """
        # Placeholder for GA4 API integration
        # In production, this would use google-analytics-data library
        return {
            "sessions": 0,
            "users": 0,
            "new_users": 0,
            "lead_magnet_views": 0,
            "events": {
                "lead_magnet_submit": 0,
                "contact_form_submit": 0,
                "booking_click": 0,
            }
        }
    
    def collect_form_metrics(self, domain: str, week_start: str) -> Dict:
        """
        Collect form submission metrics.
        Sources: WordPress database, form plugins, email notifications
        """
        # Placeholder for form metrics collection
        # In production, this would query WordPress database or form plugin APIs
        return {
            "lead_magnet_submits": 0,
            "contact_submits": 0,
        }
    
    def collect_payment_metrics(self, domain: str, week_start: str) -> Dict:
        """
        Collect payment/booking metrics.
        Sources: Stripe API, Calendly API, WordPress booking plugins
        """
        # Placeholder for payment metrics collection
        # In production, this would integrate with Stripe/Calendly APIs
        return {
            "bookings": 0,
            "deposits_paid": 0,
            "revenue": 0.0,
        }
    
    def collect_metrics_for_website(self, domain: str, week_start: str) -> WeeklyMetrics:
        """Collect all metrics for a website for a given week."""
        week_end = (datetime.strptime(week_start, "%Y-%m-%d") + timedelta(days=6)).strftime("%Y-%m-%d")
        
        # Collect from different sources
        ga4_metrics = self.collect_ga4_metrics(domain, week_start, week_end)
        form_metrics = self.collect_form_metrics(domain, week_start)
        payment_metrics = self.collect_payment_metrics(domain, week_start)
        
        # Aggregate metrics
        metrics = WeeklyMetrics(
            week_starting=week_start,
            domain=domain,
            sessions=ga4_metrics.get("sessions", 0),
            users=ga4_metrics.get("users", 0),
            new_users=ga4_metrics.get("new_users", 0),
            lead_magnet_views=ga4_metrics.get("lead_magnet_views", 0),
            lead_magnet_submits=form_metrics.get("lead_magnet_submits", 0) + ga4_metrics.get("events", {}).get("lead_magnet_submit", 0),
            contact_submits=form_metrics.get("contact_submits", 0) + ga4_metrics.get("events", {}).get("contact_form_submit", 0),
            booking_clicks=ga4_metrics.get("events", {}).get("booking_click", 0),
            bookings=payment_metrics.get("bookings", 0),
            deposits_paid=payment_metrics.get("deposits_paid", 0),
            revenue=payment_metrics.get("revenue", 0.0),
            notes="",
        )
        
        return metrics
    
    def generate_weekly_report(self, week_start: str, websites: Optional[List[str]] = None) -> Dict:
        """Generate weekly metrics report for all websites."""
        if websites is None:
            websites = WEBSITES
        
        report = {
            "week_starting": week_start,
            "generated_at": datetime.now().isoformat(),
            "websites": {},
        }
        
        for domain in websites:
            metrics = self.collect_metrics_for_website(domain, week_start)
            report["websites"][domain] = asdict(metrics)
        
        return report
    
    def save_weekly_report(self, report: Dict) -> Path:
        """Save weekly report to file."""
        week_start = report["week_starting"]
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        report_file = self.metrics_dir / f"weekly_metrics_{week_start}_{timestamp}.json"
        report_file.write_text(json.dumps(report, indent=2), encoding="utf-8")
        return report_file
    
    def generate_markdown_dashboard(self, report: Dict) -> str:
        """Generate markdown dashboard from metrics report."""
        week_start = report["week_starting"]
        
        lines = [
            f"# Weekly Metrics Dashboard - Week Starting {week_start}",
            "",
            "| Domain | Sessions | Users | New Users | Lead Magnet Views | Lead Magnet Submits | Contact Submits | Booking Clicks | Bookings | Deposits Paid | Revenue | Notes |",
            "|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|---:|---|",
        ]
        
        for domain, metrics in report["websites"].items():
            row = [
                domain,
                str(metrics["sessions"]),
                str(metrics["users"]),
                str(metrics["new_users"]),
                str(metrics["lead_magnet_views"]),
                str(metrics["lead_magnet_submits"]),
                str(metrics["contact_submits"]),
                str(metrics["booking_clicks"]),
                str(metrics["bookings"]),
                str(metrics["deposits_paid"]),
                f"${metrics['revenue']:.2f}",
                metrics.get("notes", ""),
            ]
            lines.append("| " + " | ".join(row) + " |")
        
        lines.extend([
            "",
            "## Summary",
            "",
            f"- **Total Sessions**: {sum(m['sessions'] for m in report['websites'].values())}",
            f"- **Total Users**: {sum(m['users'] for m in report['websites'].values())}",
            f"- **Total Lead Magnet Submits**: {sum(m['lead_magnet_submits'] for m in report['websites'].values())}",
            f"- **Total Contact Submits**: {sum(m['contact_submits'] for m in report['websites'].values())}",
            f"- **Total Revenue**: ${sum(m['revenue'] for m in report['websites'].values()):.2f}",
            "",
            f"*Report generated: {report['generated_at']}*",
        ])
        
        return "\n".join(lines)
    
    def save_markdown_dashboard(self, dashboard: str, week_start: str) -> Path:
        """Save markdown dashboard to file."""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        dashboard_file = self.metrics_dir / f"weekly_dashboard_{week_start}_{timestamp}.md"
        dashboard_file.write_text(dashboard, encoding="utf-8")
        return dashboard_file


def get_current_week_start() -> str:
    """Get the start date (Monday) of the current week."""
    today = datetime.now()
    days_since_monday = today.weekday()
    week_start = today - timedelta(days=days_since_monday)
    return week_start.strftime("%Y-%m-%d")


def get_previous_week_start() -> str:
    """Get the start date of the previous week."""
    current_week = datetime.strptime(get_current_week_start(), "%Y-%m-%d")
    previous_week = current_week - timedelta(days=7)
    return previous_week.strftime("%Y-%m-%d")


if __name__ == "__main__":
    import sys
    
    project_root = Path(__file__).parent.parent
    collector = MetricsCollector(project_root)
    
    # Determine which week to collect
    if len(sys.argv) > 1:
        week_start = sys.argv[1]
    else:
        # Default to previous week (most recent complete week)
        week_start = get_previous_week_start()
    
    print(f"📊 Collecting metrics for week starting {week_start}...")
    print(f"📁 Processing {len(WEBSITES)} websites...\n")
    
    # Generate report
    report = collector.generate_weekly_report(week_start)
    
    # Save JSON report
    json_file = collector.save_weekly_report(report)
    print(f"✅ JSON report saved: {json_file.relative_to(project_root)}")
    
    # Generate and save markdown dashboard
    dashboard = collector.generate_markdown_dashboard(report)
    md_file = collector.save_markdown_dashboard(dashboard, week_start)
    print(f"✅ Markdown dashboard saved: {md_file.relative_to(project_root)}")
    
    # Print summary
    print(f"\n📊 Weekly Metrics Summary:")
    print(f"   Week Starting: {week_start}")
    print(f"   Total Sessions: {sum(m['sessions'] for m in report['websites'].values())}")
    print(f"   Total Users: {sum(m['users'] for m in report['websites'].values())}")
    print(f"   Total Revenue: ${sum(m['revenue'] for m in report['websites'].values()):.2f}")
    
    print(f"\n⚠️  Note: This is a template implementation.")
    print(f"   To collect real data, configure:")
    print(f"   1. GA4 API credentials and property IDs")
    print(f"   2. Form submission tracking (WordPress database or plugin APIs)")
    print(f"   3. Payment system APIs (Stripe, Calendly, etc.)")

