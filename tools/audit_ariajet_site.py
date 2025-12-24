#!/usr/bin/env python3
"""
Quick Audit for ariajet.site
============================
"""

import requests
import ssl
import socket
import re
from datetime import datetime
from typing import Dict, Any

def audit_ariajet_site() -> Dict[str, Any]:
    """Perform comprehensive audit of ariajet.site"""
    url = "https://ariajet.site"
    domain = "ariajet.site"
    
    results = {
        "domain": domain,
        "url": url,
        "timestamp": datetime.now().isoformat(),
        "issues": [],
        "warnings": [],
        "passed": []
    }
    
    print(f"\n🔍 AUDITING: {url}")
    print("=" * 60)
    
    # 1. HTTP Status & Response Time
    try:
        start_time = datetime.now()
        response = requests.get(url, timeout=10, allow_redirects=True)
        end_time = datetime.now()
        response_time = (end_time - start_time).total_seconds()
        
        results["http_status"] = response.status_code
        results["response_time"] = round(response_time, 2)
        results["content_length"] = len(response.content)
        
        if response.status_code == 200:
            results["passed"].append("HTTP Status: 200 OK")
        else:
            results["issues"].append(f"HTTP Status: {response.status_code}")
            
        if response_time > 3.0:
            results["warnings"].append(f"Slow response time: {response_time:.2f}s")
        else:
            results["passed"].append(f"Response time: {response_time:.2f}s")
            
    except Exception as e:
        results["issues"].append(f"Connection failed: {str(e)}")
        return results
    
    # 2. SSL Certificate
    try:
        context = ssl.create_default_context()
        with socket.create_connection((domain, 443), timeout=5) as sock:
            with context.wrap_socket(sock, server_hostname=domain) as ssock:
                cert = ssock.getpeercert()
                results["ssl_valid"] = True
                results["passed"].append("SSL Certificate: Valid")
    except Exception as e:
        results["issues"].append(f"SSL Error: {str(e)}")
    
    # 3. SEO Checks
    html = response.text
    
    # Title tag
    title_match = re.search(r'<title[^>]*>(.*?)</title>', html, re.IGNORECASE | re.DOTALL)
    if title_match:
        title = title_match.group(1).strip()
        results["title"] = title
        title_len = len(title)
        
        if not title or title == domain or title.endswith("-"):
            results["issues"].append(f"Title tag incomplete or default: '{title}'")
        elif title_len < 30:
            results["warnings"].append(f"Title too short ({title_len} chars, recommended 30-60)")
        elif title_len > 60:
            results["warnings"].append(f"Title too long ({title_len} chars, recommended 30-60)")
        else:
            results["passed"].append(f"Title tag: {title_len} chars (good)")
    else:
        results["issues"].append("Title tag: NOT FOUND")
    
    # Meta description
    meta_desc_match = re.search(r'<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']', html, re.IGNORECASE)
    if meta_desc_match:
        meta_desc = meta_desc_match.group(1).strip()
        results["meta_description"] = meta_desc
        desc_len = len(meta_desc)
        
        if desc_len < 120:
            results["warnings"].append(f"Meta description too short ({desc_len} chars, recommended 120-160)")
        elif desc_len > 160:
            results["warnings"].append(f"Meta description too long ({desc_len} chars, recommended 120-160)")
        else:
            results["passed"].append(f"Meta description: {desc_len} chars (good)")
    else:
        results["issues"].append("Meta description: NOT FOUND")
    
    # H1 headings
    h1_matches = re.findall(r'<h1[^>]*>(.*?)</h1>', html, re.IGNORECASE | re.DOTALL)
    h1_count = len(h1_matches)
    results["h1_count"] = h1_count
    results["h1_headings"] = [re.sub(r'<[^>]+>', '', h).strip() for h in h1_matches]
    
    if h1_count == 0:
        results["issues"].append("No H1 heading found")
    elif h1_count > 1:
        results["warnings"].append(f"Multiple H1 headings ({h1_count}), recommended: 1")
    else:
        results["passed"].append(f"H1 heading: {h1_count} (good)")
    
    # 4. Security Headers
    headers = response.headers
    security_headers = {
        "Strict-Transport-Security": "HSTS",
        "X-Frame-Options": "Clickjacking protection",
        "X-Content-Type-Options": "MIME sniffing protection",
        "Content-Security-Policy": "CSP"
    }
    
    for header, description in security_headers.items():
        if header in headers:
            results["passed"].append(f"Security header: {header} present")
        else:
            results["warnings"].append(f"Missing security header: {header} ({description})")
    
    # 5. Content Check
    if len(html) < 1000:
        results["warnings"].append(f"Page content very small ({len(html)} bytes)")
    
    # Check for empty content areas
    main_content = re.search(r'<main[^>]*>(.*?)</main>', html, re.IGNORECASE | re.DOTALL)
    if main_content:
        main_text = re.sub(r'<[^>]+>', '', main_content.group(1)).strip()
        if len(main_text) < 100:
            results["warnings"].append("Main content area appears sparse")
    
    return results

def print_results(results: Dict[str, Any]):
    """Print audit results in a readable format"""
    print(f"\n📊 AUDIT RESULTS FOR: {results['domain']}")
    print("=" * 60)
    
    print(f"\n✅ PASSED ({len(results['passed'])}):")
    for item in results['passed']:
        print(f"   ✓ {item}")
    
    if results['warnings']:
        print(f"\n⚠️  WARNINGS ({len(results['warnings'])}):")
        for item in results['warnings']:
            print(f"   ⚠ {item}")
    
    if results['issues']:
        print(f"\n❌ ISSUES ({len(results['issues'])}):")
        for item in results['issues']:
            print(f"   ✗ {item}")
    
    print("\n" + "=" * 60)
    print(f"Summary: {len(results['passed'])} passed, {len(results['warnings'])} warnings, {len(results['issues'])} issues")

if __name__ == "__main__":
    results = audit_ariajet_site()
    print_results(results)

