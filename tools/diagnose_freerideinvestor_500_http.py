#!/usr/bin/env python3
"""
Diagnose freerideinvestor.com HTTP 500 Error (HTTP-based)
==========================================================

HTTP-based diagnostic tool for freerideinvestor.com HTTP 500 error.
Uses HTTP requests and file analysis instead of SSH.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import json
import requests
from pathlib import Path
from typing import Dict, Optional

try:
    import requests
    REQUESTS_AVAILABLE = True
except ImportError:
    REQUESTS_AVAILABLE = False


def check_http_response() -> Dict:
    """Check HTTP response and headers."""
    url = "https://freerideinvestor.com"
    
    try:
        response = requests.get(url, timeout=10, allow_redirects=True)
        return {
            "status_code": response.status_code,
            "headers": dict(response.headers),
            "content_length": len(response.content),
            "content_preview": response.text[:500] if response.text else "",
            "url": response.url
        }
    except Exception as e:
        return {"error": str(e)}


def check_wp_admin() -> Dict:
    """Check if wp-admin is accessible."""
    url = "https://freerideinvestor.com/wp-admin"
    
    try:
        response = requests.get(url, timeout=10, allow_redirects=False)
        return {
            "status_code": response.status_code,
            "location": response.headers.get("Location", ""),
            "accessible": response.status_code in [200, 302, 301]
        }
    except Exception as e:
        return {"error": str(e)}


def check_wp_login() -> Dict:
    """Check if wp-login.php is accessible."""
    url = "https://freerideinvestor.com/wp-login.php"
    
    try:
        response = requests.get(url, timeout=10)
        return {
            "status_code": response.status_code,
            "has_login_form": "loginform" in response.text.lower() or "wp-login" in response.text.lower(),
            "content_length": len(response.content)
        }
    except Exception as e:
        return {"error": str(e)}


def check_robots_txt() -> Dict:
    """Check robots.txt for clues."""
    url = "https://freerideinvestor.com/robots.txt"
    
    try:
        response = requests.get(url, timeout=10)
        return {
            "status_code": response.status_code,
            "exists": response.status_code == 200,
            "content": response.text[:500] if response.text else ""
        }
    except Exception as e:
        return {"error": str(e)}


def check_xmlrpc() -> Dict:
    """Check if XML-RPC is accessible (can indicate WordPress status)."""
    url = "https://freerideinvestor.com/xmlrpc.php"
    
    try:
        response = requests.post(url, data="<?xml version='1.0'?><methodCall><methodName>system.listMethods</methodName></methodCall>", 
                               headers={"Content-Type": "text/xml"}, timeout=10)
        return {
            "status_code": response.status_code,
            "responds": response.status_code == 200,
            "content": response.text[:200] if response.text else ""
        }
    except Exception as e:
        return {"error": str(e)}


def analyze_error_patterns(content: str) -> Dict:
    """Analyze content for common error patterns."""
    patterns = {
        "fatal_error": "Fatal error" in content or "PHP Fatal" in content,
        "parse_error": "Parse error" in content or "syntax error" in content,
        "database_error": "database" in content.lower() and "error" in content.lower(),
        "memory_error": "memory" in content.lower() and ("exhausted" in content.lower() or "limit" in content.lower()),
        "plugin_error": "plugin" in content.lower() and "error" in content.lower(),
        "theme_error": "theme" in content.lower() and "error" in content.lower(),
        "blank_page": len(content.strip()) == 0 or content.strip() == ""
    }
    
    return patterns


def main():
    """Main diagnostic execution."""
    print("=" * 70)
    print("🔍 FREERIDEINVESTOR.COM HTTP 500 ERROR DIAGNOSTIC (HTTP-based)")
    print("=" * 70)
    print()
    
    if not REQUESTS_AVAILABLE:
        print("❌ requests library required. Install with: pip install requests")
        return 1
    
    diagnostics = {}
    
    # Run HTTP-based diagnostics
    print("📋 Running HTTP-based diagnostics...")
    print()
    
    print("1️⃣  Checking main site HTTP response...")
    http_response = check_http_response()
    diagnostics["http_response"] = http_response
    if "error" in http_response:
        print(f"   ❌ Error: {http_response['error']}")
    else:
        print(f"   Status: {http_response.get('status_code', 'Unknown')}")
        print(f"   Content length: {http_response.get('content_length', 0)} bytes")
        if http_response.get('content_preview'):
            print(f"   Content preview: {http_response['content_preview'][:100]}...")
        
        # Analyze for error patterns
        patterns = analyze_error_patterns(http_response.get('content_preview', ''))
        diagnostics["error_patterns"] = patterns
        if any(patterns.values()):
            print("   ⚠️  Error patterns detected:")
            for pattern, detected in patterns.items():
                if detected:
                    print(f"      - {pattern}")
    print()
    
    print("2️⃣  Checking wp-admin accessibility...")
    wp_admin = check_wp_admin()
    diagnostics["wp_admin"] = wp_admin
    if "error" in wp_admin:
        print(f"   ❌ Error: {wp_admin['error']}")
    else:
        print(f"   Status: {wp_admin.get('status_code', 'Unknown')}")
        print(f"   Accessible: {wp_admin.get('accessible', False)}")
        if wp_admin.get('location'):
            print(f"   Redirects to: {wp_admin['location']}")
    print()
    
    print("3️⃣  Checking wp-login.php...")
    wp_login = check_wp_login()
    diagnostics["wp_login"] = wp_login
    if "error" in wp_login:
        print(f"   ❌ Error: {wp_login['error']}")
    else:
        print(f"   Status: {wp_login.get('status_code', 'Unknown')}")
        print(f"   Has login form: {wp_login.get('has_login_form', False)}")
    print()
    
    print("4️⃣  Checking robots.txt...")
    robots = check_robots_txt()
    diagnostics["robots"] = robots
    if "error" in robots:
        print(f"   ❌ Error: {robots['error']}")
    else:
        print(f"   Status: {robots.get('status_code', 'Unknown')}")
        print(f"   Exists: {robots.get('exists', False)}")
    print()
    
    print("5️⃣  Checking XML-RPC (WordPress API)...")
    xmlrpc = check_xmlrpc()
    diagnostics["xmlrpc"] = xmlrpc
    if "error" in xmlrpc:
        print(f"   ❌ Error: {xmlrpc['error']}")
    else:
        print(f"   Status: {xmlrpc.get('status_code', 'Unknown')}")
        print(f"   Responds: {xmlrpc.get('responds', False)}")
    print()
    
    # Save diagnostics report
    report_path = Path(__file__).parent.parent / "docs" / "freerideinvestor_500_http_diagnostic.json"
    report_path.parent.mkdir(parents=True, exist_ok=True)
    
    with open(report_path, 'w', encoding='utf-8') as f:
        json.dump(diagnostics, f, indent=2)
    
    print("=" * 70)
    print("📊 DIAGNOSTIC SUMMARY")
    print("=" * 70)
    
    # Recommendations based on findings
    recommendations = []
    
    if http_response.get('status_code') == 500:
        recommendations.append("✅ Confirmed: Site returning HTTP 500")
        if http_response.get('content_length', 0) == 0:
            recommendations.append("⚠️  Blank response suggests PHP fatal error or server misconfiguration")
        if diagnostics.get('error_patterns', {}).get('fatal_error'):
            recommendations.append("🔧 PHP Fatal Error detected - check PHP version compatibility")
        if diagnostics.get('error_patterns', {}).get('database_error'):
            recommendations.append("🔧 Database error detected - check database credentials in wp-config.php")
        if diagnostics.get('error_patterns', {}).get('memory_error'):
            recommendations.append("🔧 Memory error detected - increase PHP memory_limit")
    
    if not wp_admin.get('accessible'):
        recommendations.append("⚠️  wp-admin not accessible - may need to fix via hosting panel")
    
    if not wp_login.get('has_login_form'):
        recommendations.append("⚠️  wp-login.php not showing login form - WordPress may not be loading")
    
    if recommendations:
        print("\n💡 Recommendations:")
        for rec in recommendations:
            print(f"   {rec}")
    
    print(f"\n📄 Report saved: {report_path}")
    print()
    print("🔧 Next Steps:")
    print("   1. Check hosting error logs via hosting panel (cPanel/Plesk)")
    print("   2. Enable WordPress debug mode via wp-config.php")
    print("   3. Check PHP version compatibility (WordPress requires PHP 7.4+)")
    print("   4. Verify database credentials in wp-config.php")
    print("   5. Check for plugin/theme conflicts (disable all plugins, switch to default theme)")
    print("   6. Check .htaccess for syntax errors")
    print("   7. Verify file permissions (644 for files, 755 for directories)")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

