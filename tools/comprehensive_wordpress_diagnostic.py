#!/usr/bin/env python3
"""
Comprehensive WordPress Error Diagnostic Tool
==============================================

Automated detection of common WordPress issues:
- Syntax errors (PHP files)
- Plugin conflicts
- Database issues
- Memory limits
- Integration with existing diagnostic tools

Generates fix recommendations and integrates with existing tools.

Author: Agent-8 (SSOT & System Integration Specialist)
Date: 2025-12-22
Task: Create comprehensive WordPress error diagnostic tool (MEDIUM priority)
"""

import json
import sys
import subprocess
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Optional, Any
import requests
from urllib.parse import urlparse

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False
    print("⚠️  SimpleWordPressDeployer not available")

try:
    from wordpress_site_health_monitor import (
        check_uptime,
        check_ssl_validity,
        load_site_registry
    )
    HEALTH_MONITOR_AVAILABLE = True
except ImportError:
    HEALTH_MONITOR_AVAILABLE = False


class WordPressDiagnostic:
    """Comprehensive WordPress diagnostic tool."""

    def __init__(self, site_domain: str, site_configs: Optional[Dict] = None):
        """Initialize diagnostic tool for a site."""
        self.site_domain = site_domain
        self.site_configs = site_configs or load_site_configs()
        self.deployer = None
        self.issues = []
        self.recommendations = []
        
        if DEPLOYER_AVAILABLE and site_domain in self.site_configs:
            self.deployer = SimpleWordPressDeployer(
                site_domain, self.site_configs
            )

    def diagnose_all(self) -> Dict[str, Any]:
        """Run all diagnostic checks."""
        print(f"\n🔍 Running comprehensive diagnostics for {self.site_domain}...")
        
        results = {
            "site": self.site_domain,
            "timestamp": datetime.now().isoformat(),
            "checks": {},
            "issues": [],
            "recommendations": [],
            "severity": "NONE"
        }
        
        # 1. Basic connectivity check
        connectivity = self.check_connectivity()
        results["checks"]["connectivity"] = connectivity
        
        if not connectivity.get("accessible"):
            results["severity"] = "CRITICAL"
            results["issues"].append({
                "type": "connectivity",
                "severity": "CRITICAL",
                "message": f"Site not accessible: {connectivity.get('error')}",
                "recommendation": "Check DNS, hosting status, or server configuration"
            })
            return results
        
        # 2. PHP syntax errors
        if self.deployer:
            syntax_errors = self.check_php_syntax()
            results["checks"]["syntax_errors"] = syntax_errors
            if syntax_errors.get("errors"):
                results["severity"] = "HIGH"
                results["issues"].extend(syntax_errors["errors"])
        
        # 3. Plugin conflicts
        if self.deployer:
            plugin_conflicts = self.check_plugin_conflicts()
            results["checks"]["plugin_conflicts"] = plugin_conflicts
            if plugin_conflicts.get("conflicts"):
                if results["severity"] == "NONE":
                    results["severity"] = "MEDIUM"
                results["issues"].extend(plugin_conflicts["conflicts"])
        
        # 4. Database issues
        if self.deployer:
            database_issues = self.check_database()
            results["checks"]["database"] = database_issues
            if database_issues.get("issues"):
                if results["severity"] in ["NONE", "MEDIUM"]:
                    results["severity"] = "HIGH"
                results["issues"].extend(database_issues["issues"])
        
        # 5. Memory limits
        if self.deployer:
            memory_issues = self.check_memory_limits()
            results["checks"]["memory"] = memory_issues
            if memory_issues.get("issues"):
                if results["severity"] == "NONE":
                    results["severity"] = "MEDIUM"
                results["issues"].extend(memory_issues["issues"])
        
        # 6. WordPress core issues
        if self.deployer:
            core_issues = self.check_wordpress_core()
            results["checks"]["wordpress_core"] = core_issues
            if core_issues.get("issues"):
                if results["severity"] in ["NONE", "MEDIUM"]:
                    results["severity"] = "MEDIUM"
                results["issues"].extend(core_issues["issues"])
        
        # 7. Error logs
        if self.deployer:
            error_logs = self.check_error_logs()
            results["checks"]["error_logs"] = error_logs
            if error_logs.get("errors"):
                if results["severity"] == "NONE":
                    results["severity"] = "LOW"
                results["issues"].extend(error_logs["errors"])
        
        # Generate recommendations
        results["recommendations"] = self.generate_recommendations(results["issues"])
        
        return results

    def check_connectivity(self) -> Dict[str, Any]:
        """Check site connectivity and basic HTTP status."""
        if HEALTH_MONITOR_AVAILABLE:
            site_url = f"https://{self.site_domain}"
            uptime = check_uptime(site_url)
            return {
                "accessible": uptime.get("status") == "UP",
                "http_status": uptime.get("http_status"),
                "response_time": uptime.get("response_time"),
                "error": None if uptime.get("status") == "UP" else uptime.get("status")
            }
        else:
            # Fallback connectivity check
            try:
                site_url = f"https://{self.site_domain}"
                response = requests.get(site_url, timeout=10, allow_redirects=True)
                return {
                    "accessible": response.status_code == 200,
                    "http_status": response.status_code,
                    "response_time": None,
                    "error": None if response.status_code == 200 else f"HTTP {response.status_code}"
                }
            except Exception as e:
                return {
                    "accessible": False,
                    "http_status": None,
                    "response_time": None,
                    "error": str(e)
                }

    def check_php_syntax(self) -> Dict[str, Any]:
        """Check PHP syntax errors in theme and plugin files."""
        if not self.deployer:
            return {"errors": [], "files_checked": 0}
        
        if not self.deployer.connect():
            return {"errors": [], "files_checked": 0, "error": "Cannot connect"}
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                # Try to get from config
                site_config = self.site_configs.get(self.site_domain, {})
                remote_path = site_config.get('remote_path', f"domains/{self.site_domain}/public_html")
            
            errors = []
            files_checked = 0
            
            # Check theme functions.php
            theme_paths = [
                f"{remote_path}/wp-content/themes/*/functions.php",
                f"{remote_path}/wp-content/themes/*/style.css"
            ]
            
            # Check plugin files
            plugin_paths = [
                f"{remote_path}/wp-content/plugins/*/*.php"
            ]
            
            # Check wp-config.php
            config_file = f"{remote_path}/wp-config.php"
            
            files_to_check = [config_file]
            
            # Use find command to locate PHP files
            find_cmd = f"find {remote_path}/wp-content/themes -name '*.php' -type f 2>/dev/null | head -20"
            theme_files = self.deployer.execute_command(find_cmd)
            if theme_files and "No such file" not in theme_files:
                files_to_check.extend([f.strip() for f in theme_files.split('\n') if f.strip()][:10])
            
            find_cmd = f"find {remote_path}/wp-content/plugins -name '*.php' -type f 2>/dev/null | head -20"
            plugin_files = self.deployer.execute_command(find_cmd)
            if plugin_files and "No such file" not in plugin_files:
                files_to_check.extend([f.strip() for f in plugin_files.split('\n') if f.strip()][:10])
            
            for file_path in files_to_check:
                if not file_path:
                    continue
                files_checked += 1
                syntax_result = self.deployer.check_php_syntax(file_path)
                if syntax_result and not syntax_result.get("valid", True):
                    errors.append({
                        "type": "syntax_error",
                        "severity": "HIGH",
                        "file": file_path,
                        "message": syntax_result.get("error", "Syntax error detected"),
                        "line": syntax_result.get("line"),
                        "recommendation": f"Fix PHP syntax error in {file_path}. Check line {syntax_result.get('line', 'unknown')}"
                    })
            
            return {
                "errors": errors,
                "files_checked": files_checked
            }
        finally:
            self.deployer.disconnect()

    def check_plugin_conflicts(self) -> Dict[str, Any]:
        """Check for plugin conflicts."""
        if not self.deployer:
            return {"conflicts": []}
        
        if not self.deployer.connect():
            return {"conflicts": [], "error": "Cannot connect"}
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                site_config = self.site_configs.get(self.site_domain, {})
                remote_path = site_config.get('remote_path', f"domains/{self.site_domain}/public_html")
            
            conflicts = []
            
            # Check for duplicate plugin names
            plugins_cmd = f"ls -d {remote_path}/wp-content/plugins/*/ 2>/dev/null | xargs -I {{}} basename {{}}"
            plugins_result = self.deployer.execute_command(plugins_cmd)
            
            if plugins_result:
                plugins = [p.strip() for p in plugins_result.split('\n') if p.strip()]
                # Check for common conflict patterns
                # This is a simplified check - full conflict detection would require
                # checking for duplicate function names, class names, etc.
                
                # Check plugin directory structure
                for plugin in plugins[:20]:  # Limit to first 20
                    plugin_path = f"{remote_path}/wp-content/plugins/{plugin}"
                    main_file = f"{plugin_path}/{plugin}.php"
                    alt_main = f"{plugin_path}/{plugin.replace('-', '_')}.php"
                    
                    # Check if main plugin file exists
                    check_cmd = f"test -f {main_file} && echo 'EXISTS' || test -f {alt_main} && echo 'EXISTS_ALT' || echo 'MISSING'"
                    result = self.deployer.execute_command(check_cmd)
                    if "MISSING" in result:
                        conflicts.append({
                            "type": "plugin_structure",
                            "severity": "MEDIUM",
                            "plugin": plugin,
                            "message": f"Plugin {plugin} missing main file",
                            "recommendation": f"Check plugin structure for {plugin}"
                        })
            
            return {"conflicts": conflicts}
        finally:
            self.deployer.disconnect()

    def check_database(self) -> Dict[str, Any]:
        """Check database connectivity and common issues."""
        if not self.deployer:
            return {"issues": []}
        
        if not self.deployer.connect():
            return {"issues": [], "error": "Cannot connect"}
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                site_config = self.site_configs.get(self.site_domain, {})
                remote_path = site_config.get('remote_path', f"domains/{self.site_domain}/public_html")
            
            issues = []
            
            # Check wp-config.php for database settings
            config_file = f"{remote_path}/wp-config.php"
            config_check = self.deployer.execute_command(f"test -f {config_file} && echo 'EXISTS' || echo 'MISSING'")
            
            if "MISSING" in config_check:
                issues.append({
                    "type": "database_config",
                    "severity": "CRITICAL",
                    "message": "wp-config.php not found",
                    "recommendation": "Restore wp-config.php or create from template"
                })
            else:
                # Check for database connection string
                config_content = self.deployer.execute_command(f"grep -i 'DB_' {config_file} | head -5")
                if not config_content or "DB_NAME" not in config_content:
                    issues.append({
                        "type": "database_config",
                        "severity": "HIGH",
                        "message": "Database configuration may be incomplete",
                        "recommendation": "Verify DB_NAME, DB_USER, DB_PASSWORD, DB_HOST in wp-config.php"
                    })
            
            return {"issues": issues}
        finally:
            self.deployer.disconnect()

    def check_memory_limits(self) -> Dict[str, Any]:
        """Check PHP memory limits."""
        if not self.deployer:
            return {"issues": []}
        
        if not self.deployer.connect():
            return {"issues": [], "error": "Cannot connect"}
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                site_config = self.site_configs.get(self.site_domain, {})
                remote_path = site_config.get('remote_path', f"domains/{self.site_domain}/public_html")
            
            issues = []
            
            # Check wp-config.php for memory limit
            config_file = f"{remote_path}/wp-config.php"
            memory_check = self.deployer.execute_command(
                f"grep -i 'WP_MEMORY_LIMIT\\|memory_limit' {config_file} 2>/dev/null || echo 'NOT_FOUND'"
            )
            
            if "NOT_FOUND" in memory_check or not memory_check.strip():
                issues.append({
                    "type": "memory_limit",
                    "severity": "MEDIUM",
                    "message": "No explicit memory limit set in wp-config.php",
                    "recommendation": "Add define('WP_MEMORY_LIMIT', '256M'); to wp-config.php if experiencing memory issues"
                })
            else:
                # Check if memory limit is too low
                if "64M" in memory_check or "128M" in memory_check:
                    issues.append({
                        "type": "memory_limit",
                        "severity": "LOW",
                        "message": "Memory limit may be too low",
                        "recommendation": "Consider increasing to 256M or 512M for better performance"
                    })
            
            return {"issues": issues}
        finally:
            self.deployer.disconnect()

    def check_wordpress_core(self) -> Dict[str, Any]:
        """Check WordPress core files and version."""
        if not self.deployer:
            return {"issues": []}
        
        if not self.deployer.connect():
            return {"issues": [], "error": "Cannot connect"}
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                site_config = self.site_configs.get(self.site_domain, {})
                remote_path = site_config.get('remote_path', f"domains/{self.site_domain}/public_html")
            
            issues = []
            
            # Check if wp-load.php exists (core file)
            wp_load = f"{remote_path}/wp-load.php"
            wp_load_check = self.deployer.execute_command(f"test -f {wp_load} && echo 'EXISTS' || echo 'MISSING'")
            
            if "MISSING" in wp_load_check:
                issues.append({
                    "type": "wordpress_core",
                    "severity": "CRITICAL",
                    "message": "WordPress core files may be missing",
                    "recommendation": "Verify WordPress installation is complete"
                })
            
            # Check wp-config.php for debug mode
            config_file = f"{remote_path}/wp-config.php"
            debug_check = self.deployer.execute_command(
                f"grep -i 'WP_DEBUG' {config_file} 2>/dev/null | head -3"
            )
            
            if debug_check and "WP_DEBUG.*true" in debug_check.replace(" ", ""):
                issues.append({
                    "type": "wordpress_debug",
                    "severity": "LOW",
                    "message": "WP_DEBUG is enabled (should be disabled in production)",
                    "recommendation": "Set WP_DEBUG to false in production for better performance"
                })
            
            return {"issues": issues}
        finally:
            self.deployer.disconnect()

    def check_error_logs(self) -> Dict[str, Any]:
        """Check WordPress error logs."""
        if not self.deployer:
            return {"errors": []}
        
        if not self.deployer.connect():
            return {"errors": [], "error": "Cannot connect"}
        
        try:
            remote_path = getattr(self.deployer, 'remote_path', '')
            if not remote_path:
                site_config = self.site_configs.get(self.site_domain, {})
                remote_path = site_config.get('remote_path', f"domains/{self.site_domain}/public_html")
            
            errors = []
            
            # Check common error log locations
            log_paths = [
                f"{remote_path}/wp-content/debug.log",
                f"{remote_path}/error_log",
                f"{remote_path}/wp-content/error_log",
            ]
            
            for log_path in log_paths:
                check_cmd = f"test -f {log_path} && tail -n 10 {log_path} 2>/dev/null || echo 'LOG_NOT_FOUND'"
                log_content = self.deployer.execute_command(check_cmd)
                
                if log_content and "LOG_NOT_FOUND" not in log_content:
                    # Check for recent errors (last 24 hours would require date parsing)
                    recent_errors = [line for line in log_content.split('\n') 
                                    if line.strip() and any(keyword in line.lower() 
                                    for keyword in ['error', 'fatal', 'warning', 'critical'])]
                    
                    if recent_errors:
                        errors.append({
                            "type": "error_log",
                            "severity": "MEDIUM",
                            "log_file": log_path,
                            "message": f"Recent errors found in {log_path}",
                            "recent_errors": recent_errors[:5],  # Last 5 errors
                            "recommendation": f"Review {log_path} for detailed error information"
                        })
            
            return {"errors": errors}
        finally:
            self.deployer.disconnect()

    def generate_recommendations(self, issues: List[Dict]) -> List[str]:
        """Generate fix recommendations from issues."""
        recommendations = []
        
        # Group by severity
        critical = [i for i in issues if i.get("severity") == "CRITICAL"]
        high = [i for i in issues if i.get("severity") == "HIGH"]
        medium = [i for i in issues if i.get("severity") == "MEDIUM"]
        
        if critical:
            recommendations.append(f"🚨 CRITICAL: {len(critical)} critical issue(s) require immediate attention")
            for issue in critical:
                recommendations.append(f"   - {issue.get('message')}: {issue.get('recommendation')}")
        
        if high:
            recommendations.append(f"⚠️  HIGH: {len(high)} high-priority issue(s) should be addressed soon")
            for issue in high[:5]:  # Limit to top 5
                recommendations.append(f"   - {issue.get('message')}: {issue.get('recommendation')}")
        
        if medium:
            recommendations.append(f"ℹ️  MEDIUM: {len(medium)} medium-priority issue(s) can be addressed when convenient")
        
        if not issues:
            recommendations.append("✅ No issues detected - site appears healthy")
        
        return recommendations

    def save_report(self, results: Dict[str, Any], output_dir: Optional[Path] = None) -> Path:
        """Save diagnostic report to file."""
        if output_dir is None:
            output_dir = Path(__file__).parent.parent / "docs" / "diagnostic_reports"
        
        output_dir.mkdir(parents=True, exist_ok=True)
        
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"{self.site_domain}_diagnostic_{timestamp}.json"
        report_path = output_dir / filename
        
        with open(report_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)
        
        # Also create a human-readable markdown report
        md_filename = f"{self.site_domain}_diagnostic_{timestamp}.md"
        md_path = output_dir / md_filename
        
        with open(md_path, 'w', encoding='utf-8') as f:
            f.write(f"# WordPress Diagnostic Report: {self.site_domain}\n\n")
            f.write(f"**Generated:** {results['timestamp']}\n")
            f.write(f"**Severity:** {results['severity']}\n\n")
            
            f.write("## Summary\n\n")
            f.write(f"- **Total Issues:** {len(results['issues'])}\n")
            f.write(f"- **Severity:** {results['severity']}\n\n")
            
            if results['issues']:
                f.write("## Issues Found\n\n")
                for issue in results['issues']:
                    f.write(f"### {issue.get('type', 'Unknown')} ({issue.get('severity', 'UNKNOWN')})\n\n")
                    f.write(f"- **Message:** {issue.get('message')}\n")
                    if issue.get('file'):
                        f.write(f"- **File:** {issue.get('file')}\n")
                    if issue.get('line'):
                        f.write(f"- **Line:** {issue.get('line')}\n")
                    f.write(f"- **Recommendation:** {issue.get('recommendation')}\n\n")
            
            if results['recommendations']:
                f.write("## Recommendations\n\n")
                for rec in results['recommendations']:
                    f.write(f"{rec}\n")
            
            f.write("\n## Detailed Checks\n\n")
            for check_name, check_result in results['checks'].items():
                f.write(f"### {check_name.replace('_', ' ').title()}\n\n")
                f.write(f"```json\n{json.dumps(check_result, indent=2)}\n```\n\n")
        
        return report_path


def main():
    """Main execution function."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description="Comprehensive WordPress Error Diagnostic Tool"
    )
    parser.add_argument(
        "--site",
        required=True,
        help="Site domain to diagnose (e.g., freerideinvestor.com)"
    )
    parser.add_argument(
        "--all",
        action="store_true",
        help="Run diagnostics on all sites in registry"
    )
    parser.add_argument(
        "--output",
        type=Path,
        help="Output directory for reports (default: docs/diagnostic_reports)"
    )
    
    args = parser.parse_args()
    
    if args.all:
        # Load site registry
        if HEALTH_MONITOR_AVAILABLE:
            registry = load_site_registry()
            sites = list(registry.keys())
        else:
            # Fallback: use site configs
            site_configs = load_site_configs()
            sites = list(site_configs.keys())
        
        print(f"🔍 Running diagnostics on {len(sites)} sites...")
        
        all_results = []
        for site in sites:
            try:
                diagnostic = WordPressDiagnostic(site)
                results = diagnostic.diagnose_all()
                all_results.append(results)
                
                # Save individual report
                report_path = diagnostic.save_report(results, args.output)
                print(f"✅ {site}: {results['severity']} severity, {len(results['issues'])} issues")
                print(f"   Report saved: {report_path}")
            except Exception as e:
                print(f"❌ {site}: Error - {e}")
        
        # Save summary report
        if args.output:
            summary_path = args.output / f"diagnostic_summary_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
            with open(summary_path, 'w', encoding='utf-8') as f:
                json.dump({"sites": all_results, "timestamp": datetime.now().isoformat()}, f, indent=2)
            print(f"\n📊 Summary report saved: {summary_path}")
    else:
        # Single site diagnostic
        diagnostic = WordPressDiagnostic(args.site)
        results = diagnostic.diagnose_all()
        
        # Print summary
        print(f"\n{'='*60}")
        print(f"Diagnostic Results: {args.site}")
        print(f"{'='*60}")
        print(f"Severity: {results['severity']}")
        print(f"Issues Found: {len(results['issues'])}")
        print(f"\nRecommendations:")
        for rec in results['recommendations']:
            print(f"  {rec}")
        
        # Save report
        report_path = diagnostic.save_report(results, args.output)
        print(f"\n📄 Full report saved: {report_path}")
        
        return 0 if results['severity'] == "NONE" else 1


if __name__ == "__main__":
    sys.exit(main())

