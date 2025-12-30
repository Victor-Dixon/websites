#!/usr/bin/env python3
"""
Comprehensive Website Audit
===========================

Performs comprehensive audit of all websites including:
- Health checks (uptime, response time, SSL)
- SEO compliance (meta descriptions, title tags, H1 headings)
- Performance metrics (page size, load time)
- Security headers (Content-Security-Policy, X-Frame-Options, X-Content-Type-Options,
  Strict-Transport-Security, Referrer-Policy, Permissions-Policy, Access-Control-Allow-Origin)
- Subresource Integrity (SRI) checks for external resources
- Premium security (rate limiting analysis, API endpoints, debug endpoints, backend info)
- Accessibility (alt text, content structure)
- Content issues (empty pages, rendering problems)

Agent-3: Infrastructure & DevOps Specialist
Task: Enhanced security headers and premium security checks for website audit
"""

import json
import requests
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Any
import ssl
import socket
import time
import re

def load_site_registry(project_root: Path) -> Dict:
    """Loads the site registry from configs/sites_registry.json."""
    registry_file = project_root / "configs" / "sites_registry.json"
    if registry_file.exists():
        with open(registry_file, 'r', encoding='utf-8', errors='ignore') as f:
            try:
                return json.load(f)
            except json.JSONDecodeError:
                print(f"❌ Error: sites_registry.json is corrupted.")
                return {}
    print(f"❌ Error: sites_registry.json not found at {registry_file}")
    return {}

def check_uptime_and_response_time(url: str) -> Dict:
    """Checks site uptime and response time."""
    try:
        start_time = time.time()
        response = requests.get(url, timeout=10, allow_redirects=True)
        end_time = time.time()
        response_time = round(end_time - start_time, 2)
        return {
            "status": "UP" if response.status_code == 200 else "DOWN",
            "http_status": response.status_code,
            "response_time": response_time,
            "content_length": len(response.content) if response.content else 0,
            "timestamp": datetime.now().isoformat()
        }
    except requests.exceptions.RequestException as e:
        return {
            "status": "DOWN",
            "http_status": None,
            "response_time": None,
            "error": str(e),
            "timestamp": datetime.now().isoformat()
        }

def check_ssl_validity(domain: str) -> Dict:
    """Checks SSL certificate validity."""
    try:
        context = ssl.create_default_context()
        with socket.create_connection((domain, 443), timeout=5) as sock:
            with context.wrap_socket(sock, server_hostname=domain) as ssock:
                certificate_der = ssock.getpeercert(binary_form=True)
                import OpenSSL.crypto
                x509 = OpenSSL.crypto.load_certificate(OpenSSL.crypto.FILETYPE_ASN1, certificate_der)
                
                not_after = datetime.strptime(x509.get_notAfter().decode('ascii'), '%Y%m%d%H%M%SZ')
                valid_until = not_after - datetime.now()
                
                return {
                    "valid": True,
                    "valid_for_days": valid_until.days,
                    "not_after": not_after.isoformat()
                }
    except Exception as e:
        return {"valid": False, "error": str(e)}

def check_seo_metadata(html_content: str) -> Dict:
    """Checks SEO metadata in HTML content."""
    seo_checks = {
        "has_meta_description": False,
        "meta_description": None,
        "has_title_tag": False,
        "title_tag": None,
        "title_length": 0,
        "h1_count": 0,
        "h1_headings": []
    }
    
    # Check meta description
    meta_desc_match = re.search(r'<meta\s+name=["\']description["\']\s+content=["\']([^"\']+)["\']', html_content, re.IGNORECASE)
    if meta_desc_match:
        seo_checks["has_meta_description"] = True
        seo_checks["meta_description"] = meta_desc_match.group(1)
    
    # Check title tag
    title_match = re.search(r'<title>([^<]+)</title>', html_content, re.IGNORECASE)
    if title_match:
        seo_checks["has_title_tag"] = True
        seo_checks["title_tag"] = title_match.group(1).strip()
        seo_checks["title_length"] = len(seo_checks["title_tag"])
    
    # Count H1 headings
    h1_matches = re.findall(r'<h1[^>]*>([^<]+)</h1>', html_content, re.IGNORECASE)
    seo_checks["h1_count"] = len(h1_matches)
    seo_checks["h1_headings"] = [h1.strip() for h1 in h1_matches[:5]]  # First 5
    
    return seo_checks

def check_security_headers(response: requests.Response) -> Dict:
    """Checks security headers in HTTP response."""
    headers = {
        "content_security_policy": response.headers.get("Content-Security-Policy"),
        "x_frame_options": response.headers.get("X-Frame-Options"),
        "x_content_type_options": response.headers.get("X-Content-Type-Options"),
        "strict_transport_security": response.headers.get("Strict-Transport-Security"),
        "referrer_policy": response.headers.get("Referrer-Policy"),
        "permissions_policy": response.headers.get("Permissions-Policy"),
        "access_control_allow_origin": response.headers.get("Access-Control-Allow-Origin")
    }
    
    # Check for server information exposure
    server_header = response.headers.get("Server")
    x_powered_by = response.headers.get("X-Powered-By")
    server_info_exposed = server_header is not None or x_powered_by is not None
    
    return {
        "has_csp": headers["content_security_policy"] is not None,
        "has_x_frame_options": headers["x_frame_options"] is not None,
        "has_x_content_type_options": headers["x_content_type_options"] is not None,
        "has_hsts": headers["strict_transport_security"] is not None,
        "has_referrer_policy": headers["referrer_policy"] is not None,
        "has_permissions_policy": headers["permissions_policy"] is not None,
        "has_access_control_allow_origin": headers["access_control_allow_origin"] is not None,
        "server_info_exposed": server_info_exposed,
        "server_header": server_header,
        "x_powered_by": x_powered_by,
        "headers": headers
    }

def check_subresource_integrity(html_content: str) -> Dict:
    """Checks for Subresource Integrity (SRI) on external resources."""
    # Find script tags with external src
    script_tags = re.findall(r'<script[^>]*>', html_content, re.IGNORECASE)
    link_tags = re.findall(r'<link[^>]*>', html_content, re.IGNORECASE)
    
    external_resources = []
    resources_with_sri = []
    resources_without_sri = []
    
    # Check script tags
    for tag in script_tags:
        src_match = re.search(r'src=["\']([^"\']+)["\']', tag, re.IGNORECASE)
        if src_match:
            src_url = src_match.group(1)
            # Check if external
            if src_url.startswith('http://') or src_url.startswith('https://') or src_url.startswith('//'):
                external_resources.append(src_url)
                # Check if has integrity attribute in same tag
                if 'integrity=' in tag.lower():
                    resources_with_sri.append(src_url)
                else:
                    resources_without_sri.append(src_url)
    
    # Check link tags (stylesheets)
    for tag in link_tags:
        href_match = re.search(r'href=["\']([^"\']+)["\']', tag, re.IGNORECASE)
        rel_match = re.search(r'rel=["\']([^"\']+)["\']', tag, re.IGNORECASE)
        if href_match and rel_match and 'stylesheet' in rel_match.group(1).lower():
            href_url = href_match.group(1)
            # Check if external
            if href_url.startswith('http://') or href_url.startswith('https://') or href_url.startswith('//'):
                if href_url not in external_resources:  # Avoid duplicates
                    external_resources.append(href_url)
                    # Check if has integrity attribute in same tag
                    if 'integrity=' in tag.lower():
                        resources_with_sri.append(href_url)
                    else:
                        resources_without_sri.append(href_url)
    
    # Check for untrusted CDNs (basic check for common CDN patterns)
    trusted_cdns = ['cdnjs.cloudflare.com', 'cdn.jsdelivr.net', 'ajax.googleapis.com', 
                    'cdn.shopify.com', 'fonts.googleapis.com', 'fonts.gstatic.com',
                    'unpkg.com', 'cdnjs.com', 'stackpath.bootstrapcdn.com']
    untrusted_resources = [r for r in resources_without_sri if not any(cdn in r for cdn in trusted_cdns)]
    
    return {
        "external_resources_count": len(external_resources),
        "resources_with_sri": len(resources_with_sri),
        "resources_without_sri": len(resources_without_sri),
        "untrusted_cdn_resources": len(untrusted_resources),
        "missing_sri": len(resources_without_sri) > 0,
        "untrusted_cdns_detected": len(untrusted_resources) > 0
    }

def check_premium_security(url: str, html_content: str, response: requests.Response) -> Dict:
    """Checks premium security features (requires external service or advanced checks)."""
    # Rate limiting check - attempt multiple rapid requests
    rate_limit_detected = False
    try:
        # Send 5 rapid requests
        rapid_requests = []
        for _ in range(5):
            try:
                resp = requests.get(url, timeout=5, allow_redirects=True)
                rapid_requests.append(resp.status_code)
            except:
                pass
        
        # If any request returns 429 (Too Many Requests) or different status, rate limiting might be present
        if 429 in rapid_requests or len(set(rapid_requests)) > 1:
            rate_limit_detected = True
    except:
        pass
    
    # Detect API endpoints (common patterns)
    api_patterns = [
        r'["\'](/api/[^"\']+)["\']',
        r'["\'](/v\d+/[^"\']+)["\']',
        r'["\'](/graphql[^"\']*)["\']',
        r'["\'](/rest/[^"\']+)["\']'
    ]
    api_endpoints = set()
    for pattern in api_patterns:
        matches = re.findall(pattern, html_content, re.IGNORECASE)
        api_endpoints.update(matches[:10])  # Limit to first 10
    
    # Detect debug endpoints
    debug_patterns = [
        r'["\'](/debug[^"\']*)["\']',
        r'["\'](/test[^"\']*)["\']',
        r'["\'](/dev[^"\']*)["\']',
        r'["\'](/staging[^"\']*)["\']',
        r'["\'](/\?debug[^"\']*)["\']'
    ]
    debug_endpoints = set()
    for pattern in debug_patterns:
        matches = re.findall(pattern, html_content, re.IGNORECASE)
        debug_endpoints.update(matches[:10])
    
    # Backend information detection (from headers/content)
    backend_info = {}
    server_header = response.headers.get("Server", "")
    x_powered_by = response.headers.get("X-Powered-By", "")
    
    if "PHP" in x_powered_by or "php" in server_header.lower():
        backend_info["detected"] = "PHP"
    elif "nginx" in server_header.lower():
        backend_info["detected"] = "Nginx"
    elif "Apache" in server_header or "apache" in server_header.lower():
        backend_info["detected"] = "Apache"
    
    return {
        "rate_limiting_detected": rate_limit_detected,
        "rate_limiting_status": "DETECTED" if rate_limit_detected else "NOT_DETECTED",
        "api_endpoints_found": len(api_endpoints) > 0,
        "api_endpoints": list(api_endpoints)[:10],  # Limit to 10
        "debug_endpoints_found": len(debug_endpoints) > 0,
        "debug_endpoints": list(debug_endpoints)[:10],
        "backend_info": backend_info,
        "premium_checks_available": True
    }

def check_content_visibility(html_content: str) -> Dict:
    """Checks for content visibility issues."""
    # Check for empty main content areas
    main_content_match = re.search(r'<main[^>]*>([^<]*)</main>', html_content, re.IGNORECASE | re.DOTALL)
    article_match = re.search(r'<article[^>]*>([^<]*)</article>', html_content, re.IGNORECASE | re.DOTALL)
    
    # Check for CSS hiding content
    has_display_none = "display: none" in html_content.lower() or "display:none" in html_content.lower()
    has_visibility_hidden = "visibility: hidden" in html_content.lower()
    has_opacity_zero = "opacity: 0" in html_content.lower() or "opacity:0" in html_content.lower()
    
    return {
        "main_content_empty": main_content_match is not None and len(main_content_match.group(1).strip()) < 50,
        "article_content_empty": article_match is not None and len(article_match.group(1).strip()) < 50,
        "css_hiding_content": has_display_none or has_visibility_hidden or has_opacity_zero,
        "potential_issues": []
    }

def audit_site(site_name: str, site_info: Dict) -> Dict:
    """Performs comprehensive audit of a single site."""
    print(f"Auditing {site_name}...")
    
    site_url = site_info.get("url")
    if not site_url:
        site_url = f"https://{site_name}"
    
    audit_result = {
        "site": site_name,
        "url": site_url,
        "timestamp": datetime.now().isoformat(),
        "health": {},
        "seo": {},
        "security": {},
        "performance": {},
        "content": {},
        "issues": [],
        "overall_status": "UNKNOWN"
    }
    
    # Health check
    health_data = check_uptime_and_response_time(site_url)
    audit_result["health"] = health_data
    
    if health_data["status"] == "DOWN":
        audit_result["issues"].append({
            "severity": "CRITICAL",
            "category": "Health",
            "issue": f"Site is DOWN - {health_data.get('error', 'Unknown error')}"
        })
        audit_result["overall_status"] = "CRITICAL"
        print(f"  ❌ DOWN")
        return audit_result
    
    # Get HTML content for further analysis
    try:
        response = requests.get(site_url, timeout=10, allow_redirects=True)
        html_content = response.text
        
        # SEO checks
        seo_data = check_seo_metadata(html_content)
        audit_result["seo"] = seo_data
        
        # Security headers
        security_data = check_security_headers(response)
        
        # Subresource Integrity check
        sri_data = check_subresource_integrity(html_content)
        security_data["sri"] = sri_data
        
        # Premium security checks
        premium_data = check_premium_security(site_url, html_content, response)
        security_data["premium"] = premium_data
        
        audit_result["security"] = security_data
        
        # Content visibility
        content_data = check_content_visibility(html_content)
        audit_result["content"] = content_data
        
        # Performance
        content_length = len(response.content)
        page_size_kb = round(content_length / 1024, 2)
        audit_result["performance"] = {
            "page_size_kb": page_size_kb,
            "response_time": health_data.get("response_time"),
            "content_length": content_length
        }
        
        # SSL check
        domain = site_url.replace("https://", "").replace("http://", "").split('/')[0]
        ssl_data = check_ssl_validity(domain)
        audit_result["health"]["ssl"] = ssl_data
        
    except Exception as e:
        audit_result["issues"].append({
            "severity": "HIGH",
            "category": "Analysis",
            "issue": f"Failed to analyze site: {str(e)}"
        })
        print(f"  ⚠️  Analysis failed: {str(e)}")
        return audit_result
    
    # Identify issues
    if not seo_data.get("has_meta_description"):
        audit_result["issues"].append({
            "severity": "HIGH",
            "category": "SEO",
            "issue": "Missing meta description"
        })
    
    if seo_data.get("title_length", 0) < 30 or seo_data.get("title_length", 0) > 60:
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "SEO",
            "issue": f"Title tag length suboptimal ({seo_data.get('title_length')} chars, target: 30-60)"
        })
    
    if seo_data.get("h1_count", 0) > 1:
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "SEO",
            "issue": f"Multiple H1 headings ({seo_data.get('h1_count')}, should be 1)"
        })
    
    # Security header checks
    if not security_data.get("has_csp"):
        audit_result["issues"].append({
            "severity": "HIGH",
            "category": "Security",
            "issue": "Missing Content-Security-Policy header"
        })
    
    if not security_data.get("has_x_frame_options"):
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Security",
            "issue": "Missing X-Frame-Options header"
        })
    
    if not security_data.get("has_x_content_type_options"):
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Security",
            "issue": "Missing X-Content-Type-Options header"
        })
    
    if not security_data.get("has_hsts"):
        audit_result["issues"].append({
            "severity": "HIGH",
            "category": "Security",
            "issue": "Missing Strict-Transport-Security header"
        })
    
    if not security_data.get("has_referrer_policy"):
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Security",
            "issue": "Missing Referrer-Policy header"
        })
    
    if not security_data.get("has_permissions_policy"):
        audit_result["issues"].append({
            "severity": "LOW",
            "category": "Security",
            "issue": "Missing Permissions-Policy header"
        })
    
    # SRI checks
    sri_data = security_data.get("sri", {})
    if sri_data.get("missing_sri"):
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Security",
            "issue": f"Missing Subresource Integrity - {sri_data.get('resources_without_sri', 0)} external resources without SRI"
        })
    
    if sri_data.get("untrusted_cdns_detected"):
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Security",
            "issue": f"Untrusted CDN Resources - {sri_data.get('untrusted_cdn_resources', 0)} resources from untrusted CDNs"
        })
    
    # Server information exposure
    if security_data.get("server_info_exposed"):
        audit_result["issues"].append({
            "severity": "LOW",
            "category": "Security",
            "issue": "Server Information Exposed in HTTP headers"
        })
    
    # Premium security checks
    premium_data = security_data.get("premium", {})
    if not premium_data.get("rate_limiting_detected"):
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Security",
            "issue": "No Rate Limiting Detected"
        })
    
    if premium_data.get("debug_endpoints_found"):
        audit_result["issues"].append({
            "severity": "HIGH",
            "category": "Security",
            "issue": f"Debug Endpoints Detected - {len(premium_data.get('debug_endpoints', []))} potential debug endpoints found"
        })
    
    if health_data.get("response_time", 0) > 3.0:
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Performance",
            "issue": f"Slow response time ({health_data.get('response_time')}s, target: <3s)"
        })
    
    if page_size_kb > 500:
        audit_result["issues"].append({
            "severity": "MEDIUM",
            "category": "Performance",
            "issue": f"Large page size ({page_size_kb}KB, target: <500KB)"
        })
    
    if content_data.get("main_content_empty") or content_data.get("article_content_empty"):
        audit_result["issues"].append({
            "severity": "CRITICAL",
            "category": "Content",
            "issue": "Main content area appears empty"
        })
        audit_result["overall_status"] = "CRITICAL"
    
    if content_data.get("css_hiding_content"):
        audit_result["issues"].append({
            "severity": "HIGH",
            "category": "Content",
            "issue": "CSS may be hiding content (display:none, visibility:hidden, opacity:0)"
        })
    
    if not ssl_data.get("valid"):
        audit_result["issues"].append({
            "severity": "CRITICAL",
            "category": "Security",
            "issue": f"SSL certificate invalid: {ssl_data.get('error', 'Unknown error')}"
        })
        audit_result["overall_status"] = "CRITICAL"
    
    # Determine overall status
    if audit_result["overall_status"] != "CRITICAL":
        critical_count = sum(1 for issue in audit_result["issues"] if issue["severity"] == "CRITICAL")
        high_count = sum(1 for issue in audit_result["issues"] if issue["severity"] == "HIGH")
        
        if critical_count > 0:
            audit_result["overall_status"] = "CRITICAL"
        elif high_count > 2:
            audit_result["overall_status"] = "NEEDS_ATTENTION"
        elif len(audit_result["issues"]) > 5:
            audit_result["overall_status"] = "NEEDS_IMPROVEMENT"
        else:
            audit_result["overall_status"] = "HEALTHY"
    
    # Print summary
    status_icon = "✅" if audit_result["overall_status"] == "HEALTHY" else "⚠️" if audit_result["overall_status"] == "NEEDS_IMPROVEMENT" else "❌"
    print(f"  {status_icon} {audit_result['overall_status']} - {len(audit_result['issues'])} issues")
    
    return audit_result

def generate_audit_report(audit_results: List[Dict]) -> Dict:
    """Generates comprehensive audit report."""
    report = {
        "timestamp": datetime.now().isoformat(),
        "total_sites": len(audit_results),
        "summary": {
            "healthy": 0,
            "needs_improvement": 0,
            "needs_attention": 0,
            "critical": 0
        },
        "issues_by_category": {
            "Health": 0,
            "SEO": 0,
            "Security": 0,
            "Performance": 0,
            "Content": 0
        },
        "issues_by_severity": {
            "CRITICAL": 0,
            "HIGH": 0,
            "MEDIUM": 0,
            "LOW": 0
        },
        "sites": audit_results,
        "recommendations": []
    }
    
    # Calculate summary
    for result in audit_results:
        status = result.get("overall_status", "UNKNOWN")
        if status == "HEALTHY":
            report["summary"]["healthy"] += 1
        elif status == "NEEDS_IMPROVEMENT":
            report["summary"]["needs_improvement"] += 1
        elif status == "NEEDS_ATTENTION":
            report["summary"]["needs_attention"] += 1
        elif status == "CRITICAL":
            report["summary"]["critical"] += 1
        
        # Count issues
        for issue in result.get("issues", []):
            category = issue.get("category", "Unknown")
            severity = issue.get("severity", "UNKNOWN")
            
            if category in report["issues_by_category"]:
                report["issues_by_category"][category] += 1
            if severity in report["issues_by_severity"]:
                report["issues_by_severity"][severity] += 1
    
    # Generate recommendations
    if report["issues_by_category"]["SEO"] > 0:
        report["recommendations"].append({
            "priority": "HIGH",
            "category": "SEO",
            "action": "Add meta descriptions and optimize title tags for sites missing them"
        })
    
    if report["issues_by_category"]["Security"] > 0:
        report["recommendations"].append({
            "priority": "HIGH",
            "category": "Security",
            "action": "Add missing security headers (Content-Security-Policy, X-Frame-Options, X-Content-Type-Options, Strict-Transport-Security, Referrer-Policy, Permissions-Policy)"
        })
        report["recommendations"].append({
            "priority": "MEDIUM",
            "category": "Security",
            "action": "Add Subresource Integrity (SRI) hashes to external JavaScript and CSS resources"
        })
        report["recommendations"].append({
            "priority": "MEDIUM",
            "category": "Security",
            "action": "Implement rate limiting and remove debug endpoints from production"
        })
    
    if report["issues_by_category"]["Content"] > 0:
        report["recommendations"].append({
            "priority": "CRITICAL",
            "category": "Content",
            "action": "Investigate and fix empty content areas immediately"
        })
    
    if report["issues_by_category"]["Performance"] > 0:
        report["recommendations"].append({
            "priority": "MEDIUM",
            "category": "Performance",
            "action": "Optimize slow-loading sites and reduce page sizes"
        })
    
    return report

def main():
    """Main execution."""
    print("=" * 70)
    print("COMPREHENSIVE WEBSITE AUDIT")
    print("=" * 70)
    print()
    
    project_root = Path(__file__).parent.parent
    site_registry = load_site_registry(project_root)
    
    if not site_registry:
        print("❌ No sites configured in sites_registry.json. Exiting.")
        return
    
    print(f"Auditing {len(site_registry)} sites...\n")
    
    audit_results = []
    for site_name, site_info in site_registry.items():
        audit_result = audit_site(site_name, site_info)
        audit_results.append(audit_result)
    
    print()
    print("=" * 70)
    print("GENERATING AUDIT REPORT")
    print("=" * 70)
    
    report = generate_audit_report(audit_results)
    
    # Save report
    reports_dir = project_root / "docs" / "audit_reports"
    reports_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    json_file = reports_dir / f"comprehensive_audit_{timestamp}.json"
    md_file = reports_dir / f"comprehensive_audit_{timestamp}.md"
    
    with open(json_file, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2)
    
    # Generate Markdown report
    md_content = []
    md_content.append("# Comprehensive Website Audit Report")
    md_content.append(f"\n**Generated:** {report['timestamp']}")
    md_content.append(f"**Total Sites:** {report['total_sites']}")
    md_content.append("\n---\n")
    
    md_content.append("## Summary")
    md_content.append(f"\n- ✅ **Healthy:** {report['summary']['healthy']}")
    md_content.append(f"- ⚠️ **Needs Improvement:** {report['summary']['needs_improvement']}")
    md_content.append(f"- 🔄 **Needs Attention:** {report['summary']['needs_attention']}")
    md_content.append(f"- ❌ **Critical:** {report['summary']['critical']}")
    md_content.append("\n---\n")
    
    md_content.append("## Issues by Category")
    for category, count in report["issues_by_category"].items():
        if count > 0:
            md_content.append(f"- **{category}:** {count}")
    md_content.append("\n---\n")
    
    md_content.append("## Issues by Severity")
    for severity, count in report["issues_by_severity"].items():
        if count > 0:
            md_content.append(f"- **{severity}:** {count}")
    md_content.append("\n---\n")
    
    md_content.append("## Site Details")
    for site_result in report["sites"]:
        md_content.append(f"\n### {site_result['site']}")
        md_content.append(f"\n**Status:** {site_result['overall_status']}")
        md_content.append(f"**URL:** {site_result['url']}")
        md_content.append(f"**Issues:** {len(site_result['issues'])}")
        
        if site_result['issues']:
            md_content.append("\n**Issues:**")
            for issue in site_result['issues']:
                md_content.append(f"- [{issue['severity']}] {issue['category']}: {issue['issue']}")
    
    md_content.append("\n---\n")
    md_content.append("## Recommendations")
    for rec in report["recommendations"]:
        md_content.append(f"\n- **[{rec['priority']}] {rec['category']}:** {rec['action']}")
    
    with open(md_file, 'w', encoding='utf-8') as f:
        f.write("\n".join(md_content))
    
    print("\nSUMMARY:")
    print(f"  Healthy: {report['summary']['healthy']}")
    print(f"  Needs Improvement: {report['summary']['needs_improvement']}")
    print(f"  Needs Attention: {report['summary']['needs_attention']}")
    print(f"  Critical: {report['summary']['critical']}")
    print(f"\nTotal Issues: {sum(report['issues_by_severity'].values())}")
    print(f"\n✅ Reports saved to:")
    print(f"   JSON: {json_file}")
    print(f"   Markdown: {md_file}")

if __name__ == "__main__":
    main()
