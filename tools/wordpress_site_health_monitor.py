#!/usr/bin/env python3
"""
WordPress Site Health Monitoring Tool
=====================================

Automated daily health checks for WordPress sites:
- Uptime monitoring
- Response time tracking
- SSL certificate validity
- WordPress version updates
- Plugin/theme conflicts
- Alert on issues
- Generate health reports

Agent-6: Coordination & Communication Specialist
Task: Create WordPress site health monitoring tool (MEDIUM priority)
"""

import json
import os
import sys
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Optional
import subprocess
import requests
from urllib.parse import urlparse

def load_site_registry() -> Dict:
    """Load site registry from configs/sites_registry.json."""
    project_root = Path(__file__).parent.parent
    registry_file = project_root / "configs" / "sites_registry.json"
    
    if not registry_file.exists():
        print(f"⚠️  Site registry not found: {registry_file}")
        return {}
    
    try:
        with open(registry_file, 'r', encoding='utf-8') as f:
            return json.load(f)
    except Exception as e:
        print(f"❌ Error loading site registry: {e}")
        return {}

def check_uptime(url: str, timeout: int = 10) -> Dict:
    """Check site uptime and response time."""
    try:
        start_time = datetime.now()
        response = requests.get(url, timeout=timeout, allow_redirects=True)
        end_time = datetime.now()
        
        response_time = (end_time - start_time).total_seconds()
        
        return {
            "status": "UP" if response.status_code == 200 else "DOWN",
            "http_status": response.status_code,
            "response_time": round(response_time, 2),
            "timestamp": datetime.now().isoformat()
        }
    except requests.exceptions.Timeout:
        return {
            "status": "TIMEOUT",
            "http_status": None,
            "response_time": timeout,
            "timestamp": datetime.now().isoformat()
        }
    except requests.exceptions.ConnectionError:
        return {
            "status": "DOWN",
            "http_status": None,
            "response_time": None,
            "timestamp": datetime.now().isoformat()
        }
    except Exception as e:
        return {
            "status": "ERROR",
            "http_status": None,
            "response_time": None,
            "error": str(e),
            "timestamp": datetime.now().isoformat()
        }

def check_ssl_validity(url: str) -> Dict:
    """Check SSL certificate validity."""
    try:
        parsed = urlparse(url)
        if parsed.scheme != 'https':
            return {
                "valid": None,
                "note": "Not HTTPS"
            }
        
        import ssl
        import socket
        
        hostname = parsed.hostname
        port = parsed.port or 443
        
        context = ssl.create_default_context()
        with socket.create_connection((hostname, port), timeout=10) as sock:
            with context.wrap_socket(sock, server_hostname=hostname) as ssock:
                cert = ssock.getpeercert()
                
                import datetime as dt
                not_after = datetime.strptime(cert['notAfter'], '%b %d %H:%M:%S %Y %Z')
                days_until_expiry = (not_after - datetime.now()).days
                
                return {
                    "valid": True,
                    "issuer": cert.get('issuer', []),
                    "subject": cert.get('subject', []),
                    "not_after": cert['notAfter'],
                    "days_until_expiry": days_until_expiry,
                    "expires_soon": days_until_expiry < 30
                }
    except Exception as e:
        return {
            "valid": False,
            "error": str(e)
        }

def check_wordpress_version(site_config: Dict) -> Dict:
    """Check WordPress version and updates."""
    # This would require WordPress REST API or SSH access
    # For now, return placeholder
    return {
        "version": "UNKNOWN",
        "needs_update": None,
        "note": "WordPress version check requires REST API or SSH access"
    }

def check_plugin_theme_conflicts(site_config: Dict) -> Dict:
    """Check for plugin/theme conflicts."""
    # This would require WordPress REST API or SSH access
    # For now, return placeholder
    return {
        "conflicts": [],
        "note": "Plugin/theme conflict check requires REST API or SSH access"
    }

def monitor_site_health(site_name: str, site_config: Dict) -> Dict:
    """Monitor health for a single site."""
    url = site_config.get('url', '')
    if not url:
        # Try to construct URL from site name
        if site_name and '.' in site_name:
            url = f"https://{site_name}"
        else:
            return {
                "site": site_name,
                "status": "ERROR",
                "error": "No URL configured and cannot construct from site name"
            }
    
    health = {
        "site": site_name,
        "url": url,
        "timestamp": datetime.now().isoformat(),
        "uptime": check_uptime(url),
        "ssl": check_ssl_validity(url),
        "wordpress": check_wordpress_version(site_config),
        "conflicts": check_plugin_theme_conflicts(site_config)
    }
    
    # Determine overall health status
    if health["uptime"]["status"] != "UP":
        health["overall_status"] = "CRITICAL"
    elif health["ssl"].get("expires_soon"):
        health["overall_status"] = "WARNING"
    elif health["uptime"]["response_time"] and health["uptime"]["response_time"] > 5:
        health["overall_status"] = "WARNING"
    else:
        health["overall_status"] = "HEALTHY"
    
    return health

def generate_health_report(health_results: List[Dict]) -> Dict:
    """Generate comprehensive health report."""
    total_sites = len(health_results)
    healthy = sum(1 for h in health_results if h.get("overall_status") == "HEALTHY")
    warning = sum(1 for h in health_results if h.get("overall_status") == "WARNING")
    critical = sum(1 for h in health_results if h.get("overall_status") == "CRITICAL")
    
    return {
        "timestamp": datetime.now().isoformat(),
        "summary": {
            "total_sites": total_sites,
            "healthy": healthy,
            "warning": warning,
            "critical": critical,
            "health_percentage": round((healthy / total_sites * 100) if total_sites > 0 else 0, 1)
        },
        "sites": health_results,
        "alerts": [
            h for h in health_results 
            if h.get("overall_status") in ["WARNING", "CRITICAL"]
        ]
    }

def main():
    """Main execution."""
    print("=" * 70)
    print("WORDPRESS SITE HEALTH MONITOR")
    print("=" * 70)
    print()
    
    project_root = Path(__file__).parent.parent
    registry = load_site_registry()
    
    if not registry:
        print("❌ No sites found in registry")
        return 1
    
    # Handle different registry structures
    if isinstance(registry, dict):
        sites = registry.get("sites", registry)  # Try "sites" key, fallback to whole dict
        if not sites or (isinstance(sites, dict) and not any(isinstance(v, dict) for v in sites.values())):
            # If registry is a list or different structure, try to extract sites
            if isinstance(registry, list):
                sites = {item.get("name", f"site_{i}"): item for i, item in enumerate(registry)}
            else:
                # Try to find site entries in the registry
                sites = {k: v for k, v in registry.items() if isinstance(v, dict) and ("url" in v or "domain" in v)}
    
    if not sites:
        print("❌ No sites configured")
        print(f"   Registry structure: {type(registry)}")
        if isinstance(registry, dict):
            print(f"   Registry keys: {list(registry.keys())[:10]}")
        return 1
    
    print(f"Monitoring {len(sites)} sites...")
    print()
    
    health_results = []
    for site_name, site_config in sites.items():
        print(f"Checking {site_name}...")
        health = monitor_site_health(site_name, site_config)
        health_results.append(health)
        
        status_icon = "✅" if health.get("overall_status") == "HEALTHY" else "⚠️" if health.get("overall_status") == "WARNING" else "❌"
        print(f"  {status_icon} {health.get('overall_status', 'UNKNOWN')}")
        if health.get("uptime", {}).get("response_time"):
            print(f"     Response time: {health['uptime']['response_time']}s")
    
    print()
    print("=" * 70)
    print("GENERATING HEALTH REPORT")
    print("=" * 70)
    
    report = generate_health_report(health_results)
    
    # Save report
    reports_dir = project_root / "docs" / "health_reports"
    reports_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    report_file = reports_dir / f"wordpress_health_report_{timestamp}.json"
    
    with open(report_file, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2)
    
    # Print summary
    print()
    print("SUMMARY:")
    print(f"  Total sites: {report['summary']['total_sites']}")
    print(f"  Healthy: {report['summary']['healthy']}")
    print(f"  Warning: {report['summary']['warning']}")
    print(f"  Critical: {report['summary']['critical']}")
    print(f"  Health percentage: {report['summary']['health_percentage']}%")
    
    if report['alerts']:
        print()
        print("ALERTS:")
        for alert in report['alerts']:
            print(f"  ⚠️  {alert['site']}: {alert.get('overall_status')}")
    
    print()
    print(f"✅ Report saved to: {report_file}")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())

