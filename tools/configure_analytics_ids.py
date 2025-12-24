#!/usr/bin/env python3
"""
Analytics ID Configuration Tool
================================

Updates placeholder GA4 Measurement IDs and Facebook Pixel IDs in deployed
WordPress analytics tracking code.

Features:
- Update GA4 Measurement IDs (G-XXXXXXXXXX)
- Update Facebook Pixel IDs
- Validate ID format
- Batch update across multiple sites
- Generate configuration report
"""

import json
import re
from pathlib import Path
from typing import Dict, List, Optional, Tuple
from datetime import datetime

# Website to theme mapping
WEBSITE_THEME_PATHS = {
    "freerideinvestor.com": "websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/functions.php",
    "houstonsipqueen.com": "websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen/functions.php",
    "tradingrobotplug.com": "websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/functions.php",
}

# GA4 Measurement ID format: G-XXXXXXXXXX (10 alphanumeric characters after G-)
GA4_ID_PATTERN = re.compile(r'G-[A-Z0-9]{10}')
GA4_PLACEHOLDER = "G-XXXXXXXXXX"

# Facebook Pixel ID format: 16-digit number
PIXEL_ID_PATTERN = re.compile(r'\d{15,16}')
PIXEL_PLACEHOLDER = "YOUR_PIXEL_ID"


def validate_ga4_id(ga4_id: str) -> bool:
    """Validate GA4 Measurement ID format."""
    return bool(GA4_ID_PATTERN.match(ga4_id))


def validate_pixel_id(pixel_id: str) -> bool:
    """Validate Facebook Pixel ID format."""
    return bool(PIXEL_ID_PATTERN.match(pixel_id))


def update_tracking_ids(
    file_path: Path,
    ga4_id: Optional[str] = None,
    pixel_id: Optional[str] = None
) -> Tuple[bool, List[str]]:
    """
    Update tracking IDs in WordPress functions.php file.
    
    Returns:
        (success: bool, messages: List[str])
    """
    if not file_path.exists():
        return False, [f"File not found: {file_path}"]
    
    content = file_path.read_text(encoding="utf-8")
    original_content = content
    messages = []
    
    # Update GA4 ID
    if ga4_id:
        if not validate_ga4_id(ga4_id):
            return False, [f"Invalid GA4 ID format: {ga4_id}. Expected format: G-XXXXXXXXXX"]
        
        if GA4_PLACEHOLDER in content:
            content = content.replace(GA4_PLACEHOLDER, ga4_id)
            messages.append(f"✅ Updated GA4 ID: {ga4_id}")
        elif GA4_ID_PATTERN.search(content):
            # Already has a GA4 ID, replace it
            content = GA4_ID_PATTERN.sub(ga4_id, content)
            messages.append(f"✅ Replaced existing GA4 ID with: {ga4_id}")
        else:
            messages.append(f"⚠️  GA4 placeholder not found in file")
    
    # Update Facebook Pixel ID
    if pixel_id:
        if not validate_pixel_id(pixel_id):
            return False, [f"Invalid Pixel ID format: {pixel_id}. Expected format: 15-16 digit number"]
        
        if PIXEL_PLACEHOLDER in content:
            content = content.replace(PIXEL_PLACEHOLDER, pixel_id)
            messages.append(f"✅ Updated Facebook Pixel ID: {pixel_id}")
        elif PIXEL_ID_PATTERN.search(content):
            # Check if it's actually a pixel ID (not just any number)
            # For now, simple replacement
            messages.append(f"⚠️  Facebook Pixel ID may already be configured")
        else:
            messages.append(f"⚠️  Facebook Pixel placeholder not found in file")
    
    # Only write if content changed
    if content != original_content:
        file_path.write_text(content, encoding="utf-8")
        return True, messages
    
    return False, messages if messages else ["⚠️  No changes made"]


def load_analytics_config(config_file: Path) -> Dict:
    """Load analytics configuration from JSON file."""
    if not config_file.exists():
        return {}
    
    return json.loads(config_file.read_text(encoding="utf-8"))


def create_config_template(config_file: Path):
    """Create a configuration template file."""
    template = {
        "ga4_property_ids": {
            "freerideinvestor.com": "G-XXXXXXXXXX",
            "houstonsipqueen.com": "G-XXXXXXXXXX",
            "tradingrobotplug.com": "G-XXXXXXXXXX",
        },
        "facebook_pixel_ids": {
            "freerideinvestor.com": "YOUR_PIXEL_ID",
            "houstonsipqueen.com": "YOUR_PIXEL_ID",
            "tradingrobotplug.com": "YOUR_PIXEL_ID",
        },
        "last_updated": datetime.now().isoformat(),
        "notes": "Replace placeholder IDs with actual values from Google Analytics and Facebook Business Manager"
    }
    
    config_file.parent.mkdir(parents=True, exist_ok=True)
    config_file.write_text(json.dumps(template, indent=2), encoding="utf-8")
    return template


def configure_analytics_for_site(
    domain: str,
    project_root: Path,
    ga4_id: Optional[str] = None,
    pixel_id: Optional[str] = None,
    config: Optional[Dict] = None
) -> Dict:
    """Configure analytics IDs for a single website."""
    result = {
        "domain": domain,
        "status": "pending",
        "ga4_updated": False,
        "pixel_updated": False,
        "messages": [],
        "errors": [],
    }
    
    try:
        # Get IDs from config if not provided
        if config:
            if not ga4_id and "ga4_property_ids" in config:
                ga4_id = config["ga4_property_ids"].get(domain)
            if not pixel_id and "facebook_pixel_ids" in config:
                pixel_id = config["facebook_pixel_ids"].get(domain)
        
        # Skip if no IDs provided
        if not ga4_id and not pixel_id:
            result["status"] = "skipped"
            result["messages"].append("No IDs provided for configuration")
            return result
        
        # Find functions.php file
        theme_path = WEBSITE_THEME_PATHS.get(domain)
        if not theme_path:
            result["status"] = "skipped"
            result["errors"].append(f"Theme path not configured for {domain}")
            return result
        
        functions_file = project_root / theme_path
        
        # Update tracking IDs
        success, messages = update_tracking_ids(functions_file, ga4_id, pixel_id)
        
        if success:
            result["status"] = "success"
            result["messages"] = messages
            result["ga4_updated"] = ga4_id is not None
            result["pixel_updated"] = pixel_id is not None
        else:
            result["status"] = "error"
            result["errors"] = messages
    
    except Exception as e:
        result["status"] = "error"
        result["errors"].append(str(e))
    
    return result


def configure_analytics_batch(
    config_file: Optional[Path] = None,
    project_root: Optional[Path] = None
) -> Dict:
    """Configure analytics IDs for all websites using config file."""
    if project_root is None:
        project_root = Path(__file__).parent.parent
    
    if config_file is None:
        config_file = project_root / "config" / "analytics_ids.json"
    
    # Load or create config
    if config_file.exists():
        config = load_analytics_config(config_file)
        print(f"📄 Loaded configuration from {config_file.relative_to(project_root)}")
    else:
        print(f"📄 Configuration file not found. Creating template: {config_file.relative_to(project_root)}")
        config = create_config_template(config_file)
        print(f"⚠️  Please update the configuration file with actual IDs, then run again.")
        return {"status": "template_created", "config_file": str(config_file)}
    
    results = {
        "timestamp": datetime.now().strftime("%Y%m%d_%H%M%S"),
        "websites": {},
        "summary": {
            "total": len(WEBSITE_THEME_PATHS),
            "success": 0,
            "skipped": 0,
            "failed": 0,
        },
    }
    
    print(f"\n🚀 Starting Analytics ID Configuration...")
    print(f"📊 Processing {len(WEBSITE_THEME_PATHS)} websites...\n")
    
    for domain in WEBSITE_THEME_PATHS.keys():
        print(f"📁 Configuring {domain}...")
        result = configure_analytics_for_site(domain, project_root, config=config)
        results["websites"][domain] = result
        
        if result["status"] == "success":
            results["summary"]["success"] += 1
            for msg in result["messages"]:
                print(f"  {msg}")
        elif result["status"] == "skipped":
            results["summary"]["skipped"] += 1
            for msg in result["messages"]:
                print(f"  {msg}")
        else:
            results["summary"]["failed"] += 1
            for error in result["errors"]:
                print(f"  ❌ {error}")
    
    # Save configuration report
    report_file = project_root / "docs" / "analytics_setup" / f"configuration_report_{results['timestamp']}.json"
    report_file.parent.mkdir(parents=True, exist_ok=True)
    report_file.write_text(json.dumps(results, indent=2), encoding="utf-8")
    
    print(f"\n✅ Configuration complete!")
    print(f"📊 Summary:")
    print(f"   Success: {results['summary']['success']}")
    print(f"   Skipped: {results['summary']['skipped']}")
    print(f"   Failed: {results['summary']['failed']}")
    print(f"\n📄 Report saved: {report_file.relative_to(project_root)}")
    
    return results


if __name__ == "__main__":
    import sys
    
    project_root = Path(__file__).parent.parent
    
    # Check for command line arguments (domain, ga4_id, pixel_id)
    if len(sys.argv) >= 4:
        domain = sys.argv[1]
        ga4_id = sys.argv[2] if sys.argv[2] != "None" else None
        pixel_id = sys.argv[3] if sys.argv[3] != "None" else None
        
        print(f"🔧 Configuring analytics for {domain}...")
        result = configure_analytics_for_site(domain, project_root, ga4_id, pixel_id)
        print(f"\n{'✅' if result['status'] == 'success' else '❌'} {result['status'].upper()}")
        if result["messages"]:
            for msg in result["messages"]:
                print(f"  {msg}")
        if result["errors"]:
            for error in result["errors"]:
                print(f"  ❌ {error}")
    else:
        # Batch configuration using config file
        configure_analytics_batch(project_root=project_root)


