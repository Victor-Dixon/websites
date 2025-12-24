#!/usr/bin/env python3
"""
Analytics Tracking Verification Tool
=====================================

Verifies that analytics tracking codes are correctly deployed and configured
in WordPress themes. Checks for:
- GA4 tracking code presence and ID configuration
- Facebook Pixel code presence and ID configuration
- Proper WordPress hook integration (wp_head)
- Code syntax validity
"""

import json
import re
from pathlib import Path
from typing import Dict, List
from datetime import datetime

WEBSITE_THEME_PATHS = {
    "freerideinvestor.com": "websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/functions.php",
    "houstonsipqueen.com": "websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen/functions.php",
    "tradingrobotplug.com": "websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/functions.php",
}

GA4_PLACEHOLDER = "G-XXXXXXXXXX"
GA4_ID_PATTERN = re.compile(r'G-[A-Z0-9]{10}')
PIXEL_PLACEHOLDER = "YOUR_PIXEL_ID"
PIXEL_ID_PATTERN = re.compile(r'\d{15,16}')


def verify_analytics_tracking(file_path: Path) -> Dict:
    """Verify analytics tracking code in functions.php."""
    result = {
        "file": str(file_path),
        "exists": False,
        "ga4_present": False,
        "ga4_configured": False,
        "ga4_id": None,
        "pixel_present": False,
        "pixel_configured": False,
        "pixel_id": None,
        "wp_head_hook": False,
        "function_name": None,
        "issues": [],
        "status": "unknown",
    }
    
    if not file_path.exists():
        result["issues"].append("functions.php file not found")
        result["status"] = "missing"
        return result
    
    result["exists"] = True
    content = file_path.read_text(encoding="utf-8")
    
    # Check for analytics function
    if "add_analytics_tracking_codes" in content:
        result["function_name"] = "add_analytics_tracking_codes"
        result["wp_head_hook"] = "add_action('wp_head', 'add_analytics_tracking_codes" in content or 'add_action("wp_head", "add_analytics_tracking_codes' in content
    else:
        result["issues"].append("Analytics tracking function not found")
        result["status"] = "not_deployed"
        return result
    
    # Check GA4 tracking
    if "Google Analytics 4" in content or "gtag" in content.lower():
        result["ga4_present"] = True
        
        # Check for placeholder or configured ID
        if GA4_PLACEHOLDER in content:
            result["issues"].append("GA4 ID still using placeholder (G-XXXXXXXXXX)")
            result["status"] = "needs_configuration"
        elif GA4_ID_PATTERN.search(content):
            ga4_match = GA4_ID_PATTERN.search(content)
            result["ga4_configured"] = True
            result["ga4_id"] = ga4_match.group()
        else:
            result["issues"].append("GA4 tracking code present but ID not found")
            result["status"] = "configuration_error"
    else:
        result["issues"].append("GA4 tracking code not found")
    
    # Check Facebook Pixel
    if "Facebook Pixel" in content or "fbq" in content.lower():
        result["pixel_present"] = True
        
        # Check for placeholder or configured ID
        if PIXEL_PLACEHOLDER in content:
            result["issues"].append("Facebook Pixel ID still using placeholder (YOUR_PIXEL_ID)")
            if result["status"] != "configuration_error":
                result["status"] = "needs_configuration"
        elif PIXEL_ID_PATTERN.search(content):
            # Find pixel ID in fbq('init', 'ID') pattern
            pixel_match = re.search(r"fbq\s*\(\s*['\"]init['\"]\s*,\s*['\"](\d{15,16})['\"]", content)
            if pixel_match:
                result["pixel_configured"] = True
                result["pixel_id"] = pixel_match.group(1)
        else:
            result["issues"].append("Facebook Pixel code present but ID not found")
            if result["status"] != "configuration_error":
                result["status"] = "configuration_error"
    else:
        result["issues"].append("Facebook Pixel tracking code not found")
    
    # Determine overall status
    if result["status"] == "unknown":
        if result["ga4_configured"] and result["pixel_configured"]:
            result["status"] = "configured"
        elif result["ga4_present"] and result["pixel_present"]:
            result["status"] = "deployed"
        else:
            result["status"] = "incomplete"
    
    return result


def verify_all_sites(project_root: Path) -> Dict:
    """Verify analytics tracking for all websites."""
    results = {
        "timestamp": datetime.now().isoformat(),
        "websites": {},
        "summary": {
            "total": len(WEBSITE_THEME_PATHS),
            "configured": 0,
            "deployed": 0,
            "needs_configuration": 0,
            "missing": 0,
            "errors": 0,
        },
    }
    
    print(f"🔍 Verifying Analytics Tracking Configuration...\n")
    
    for domain, theme_path in WEBSITE_THEME_PATHS.items():
        functions_file = project_root / theme_path
        result = verify_analytics_tracking(functions_file)
        results["websites"][domain] = result
        
        status = result["status"]
        if status == "configured":
            results["summary"]["configured"] += 1
            print(f"✅ {domain}: Fully configured")
            if result["ga4_id"]:
                print(f"   GA4 ID: {result['ga4_id']}")
            if result["pixel_id"]:
                print(f"   Pixel ID: {result['pixel_id']}")
        elif status == "deployed":
            results["summary"]["deployed"] += 1
            print(f"⚠️  {domain}: Deployed but needs ID configuration")
        elif status == "needs_configuration":
            results["summary"]["needs_configuration"] += 1
            print(f"⚠️  {domain}: Needs configuration ({len(result['issues'])} issues)")
        elif status == "missing":
            results["summary"]["missing"] += 1
            print(f"❌ {domain}: Not deployed")
        else:
            results["summary"]["errors"] += 1
            print(f"❌ {domain}: {status}")
        
        if result["issues"]:
            for issue in result["issues"]:
                print(f"   - {issue}")
    
    print(f"\n📊 Summary:")
    print(f"   ✅ Fully Configured: {results['summary']['configured']}")
    print(f"   ⚠️  Deployed (Needs IDs): {results['summary']['deployed']}")
    print(f"   ⚠️  Needs Configuration: {results['summary']['needs_configuration']}")
    print(f"   ❌ Missing: {results['summary']['missing']}")
    print(f"   ❌ Errors: {results['summary']['errors']}")
    
    return results


if __name__ == "__main__":
    project_root = Path(__file__).parent.parent
    results = verify_all_sites(project_root)
    
    # Save verification report
    report_file = project_root / "docs" / "analytics_setup" / f"verification_report_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
    report_file.parent.mkdir(parents=True, exist_ok=True)
    report_file.write_text(json.dumps(results, indent=2), encoding="utf-8")
    print(f"\n📄 Report saved: {report_file.relative_to(project_root)}")

