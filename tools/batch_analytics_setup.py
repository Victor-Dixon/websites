#!/usr/bin/env python3
"""
Batch Analytics Setup Tool
==========================

Implements GA4, Facebook Pixel, UTM tracking, and metrics dashboard setup
for multiple websites in a single batch operation.

Sites:
- crosbyultimateevents.com
- dadudekc.com
- freerideinvestor.com
- houstonsipqueen.com
- tradingrobotplug.com
"""

import json
import os
from pathlib import Path
from typing import Dict, List
from datetime import datetime

# Website configuration
WEBSITES = {
    "crosbyultimateevents.com": {
        "events": ["lead_magnet_submit", "contact_form_submit", "booking_click", "phone_click"],
        "optional_events": ["booking_complete", "deposit_paid"],
        "utm_campaigns": ["leadmagnet_checklist", "home_abtest_A", "holiday_events", "corporate_q1"],
    },
    "dadudekc.com": {
        "events": ["lead_magnet_submit", "contact_form_submit", "booking_click", "phone_click"],
        "optional_events": ["sprint_booking_complete", "deposit_paid"],
        "utm_campaigns": ["leadmagnet_audit", "homepage_optimization", "automation_focus"],
    },
    "freerideinvestor.com": {
        "events": ["lead_magnet_submit", "subscription_form_submit", "premium_upgrade_click"],
        "optional_events": ["premium_membership_purchased", "trading_guide_download"],
        "utm_campaigns": ["trading_roadmap", "premium_upsell", "trading_content"],
    },
    "houstonsipqueen.com": {
        "events": ["lead_magnet_submit", "quote_form_submit", "booking_click", "phone_click"],
        "optional_events": ["booking_complete", "deposit_paid"],
        "utm_campaigns": ["leadmagnet_checklist", "luxury_bartending", "event_planning"],
    },
    "tradingrobotplug.com": {
        "events": ["waitlist_submit", "contact_form_submit"],
        "optional_events": ["waitlist_signup", "demo_request"],
        "utm_campaigns": ["waitlist_launch", "pre_launch_content", "trading_validation"],
    },
}

def generate_ga4_code(domain: str, config: Dict) -> str:
    """Generate GA4 tracking code snippet."""
    events_lines = []
    for event in config["events"]:
        events_lines.extend([
            f'  // Track {event} event',
            f'  gtag("event", "{event}", {{',
            f'    "event_category": "engagement",',
            f'    "event_label": "{event}"',
            f'  }});',
            ''
        ])
    events_code = "\n".join(events_lines)
    
    return f"""<!-- Google Analytics 4 (GA4) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){{dataLayer.push(arguments);}}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX', {{
    'page_path': window.location.pathname,
    'page_title': document.title,
  }});
  
  // Custom Events Tracking
{events_code}
</script>
<!-- End GA4 -->"""


def generate_facebook_pixel_code() -> str:
    """Generate Facebook Pixel base code."""
    return """<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {{if(f.fbq)return;n=f.fbq=function(){{n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)}};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', 'YOUR_PIXEL_ID');
  fbq('track', 'PageView');
</script>
<noscript>
  <img height="1" width="1" style="display:none"
       src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->"""


def generate_utm_tracking_guide(domain: str, config: Dict) -> str:
    """Generate UTM tracking guide for a website."""
    campaigns = ", ".join([f'"{c}"' for c in config["utm_campaigns"]])
    
    return f"""# UTM Tracking Guide - {domain}

## UTM Parameter Conventions

### Required Parameters:
- `utm_source`: Traffic source (instagram, facebook, linkedin, email, website, etc.)
- `utm_medium`: Traffic medium (social, bio, post, story, cpc, hero, footer, newsletter)
- `utm_campaign`: Campaign identifier ({campaigns}, etc.)

### Optional Parameters:
- `utm_content`: Creative identifier (cta_primary, cta_secondary, video_01, carousel_02)

## Example URLs:

### Instagram Bio → Lead Magnet:
```
https://{domain}/lead-magnet/?utm_source=instagram&utm_medium=bio&utm_campaign={config["utm_campaigns"][0]}
```

### Email Newsletter → Homepage:
```
https://{domain}/?utm_source=email&utm_medium=newsletter&utm_campaign={config["utm_campaigns"][0]}
```

### Facebook Ad → Contact:
```
https://{domain}/contact/?utm_source=facebook&utm_medium=cpc&utm_campaign={config["utm_campaigns"][0]}&utm_content=ad_01
```

## Tracking Setup:
1. Add UTM parameters to all external links (social media, emails, ads)
2. Track UTM parameters in GA4 under "Acquisition" → "Traffic acquisition"
3. Create custom reports for UTM campaign performance
4. Monitor UTM performance weekly in metrics dashboard
"""


def generate_metrics_dashboard_template(domain: str) -> str:
    """Generate weekly metrics dashboard template."""
    return f"""# Weekly Metrics Dashboard - {domain}

| Week Starting | Sessions | Users | New Users | Lead Magnet Views | Lead Magnet Submits | Contact Submits | Booking Clicks | Bookings | Deposits Paid | Revenue | Notes |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|---:|---|
| YYYY-MM-DD | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | $0.00 | | Initial setup |

## Metrics Definitions:
- **Sessions**: Total visits to the website
- **Users**: Unique visitors
- **New Users**: First-time visitors
- **Lead Magnet Views**: Page views of lead magnet landing page
- **Lead Magnet Submits**: Form submissions for lead magnet
- **Contact Submits**: Contact form submissions
- **Booking Clicks**: Clicks on booking/calendar links
- **Bookings**: Completed bookings
- **Deposits Paid**: Total deposits received
- **Revenue**: Total revenue generated

## Data Sources:
- **GA4**: Sessions, Users, New Users, Page Views, Events
- **Form Tracking**: Lead Magnet Submits, Contact Submits
- **Payment System**: Bookings, Deposits Paid, Revenue
"""


def create_implementation_files():
    """Create implementation files for all websites."""
    project_root = Path(__file__).parent.parent
    output_dir = project_root / "docs" / "analytics_setup"
    output_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    
    results = {
        "timestamp": timestamp,
        "websites": {},
        "files_created": [],
    }
    
    # Generate files for each website
    for domain, config in WEBSITES.items():
        site_dir = output_dir / domain
        site_dir.mkdir(parents=True, exist_ok=True)
        
        # GA4 code
        ga4_file = site_dir / "ga4_tracking_code.html"
        ga4_file.write_text(generate_ga4_code(domain, config), encoding="utf-8")
        results["files_created"].append(str(ga4_file.relative_to(project_root)))
        
        # Facebook Pixel code
        pixel_file = site_dir / "facebook_pixel_code.html"
        pixel_file.write_text(generate_facebook_pixel_code(), encoding="utf-8")
        results["files_created"].append(str(pixel_file.relative_to(project_root)))
        
        # UTM tracking guide
        utm_file = site_dir / "utm_tracking_guide.md"
        utm_file.write_text(generate_utm_tracking_guide(domain, config), encoding="utf-8")
        results["files_created"].append(str(utm_file.relative_to(project_root)))
        
        # Metrics dashboard template
        metrics_file = site_dir / "weekly_metrics_dashboard.md"
        metrics_file.write_text(generate_metrics_dashboard_template(domain), encoding="utf-8")
        results["files_created"].append(str(metrics_file.relative_to(project_root)))
        
        results["websites"][domain] = {
            "events": config["events"],
            "optional_events": config["optional_events"],
            "utm_campaigns": config["utm_campaigns"],
            "files": [
                str(ga4_file.name),
                str(pixel_file.name),
                str(utm_file.name),
                str(metrics_file.name),
            ],
        }
    
    # Create summary report
    summary_file = output_dir / f"batch_analytics_setup_summary_{timestamp}.json"
    summary_file.write_text(json.dumps(results, indent=2), encoding="utf-8")
    
    # Create implementation guide
    implementation_guide = generate_implementation_guide(results)
    guide_file = output_dir / "BATCH_ANALYTICS_IMPLEMENTATION_GUIDE.md"
    guide_file.write_text(implementation_guide, encoding="utf-8")
    results["files_created"].append(str(guide_file.relative_to(project_root)))
    
    return results


def generate_implementation_guide(results: Dict) -> str:
    """Generate comprehensive implementation guide."""
    websites_list = "\n".join([f"- {domain}" for domain in results["websites"].keys()])
    timestamp = results["timestamp"]
    
    return f"""# Batch Analytics Setup Implementation Guide

**Generated:** {timestamp}  
**Sites:** {len(results["websites"])} websites  
**Status:** Ready for implementation

## Overview

This batch implementation covers GA4, Facebook Pixel, UTM tracking, and metrics dashboard setup for:
{websites_list}

## Implementation Steps

### 1. GA4 Setup

For each website:
1. Create GA4 property in Google Analytics
2. Copy GA4 Measurement ID (format: G-XXXXXXXXXX)
3. Add GA4 tracking code to WordPress theme header (via functions.php or header.php)
4. Replace `G-XXXXXXXXXX` placeholder in generated code with actual Measurement ID
5. Verify tracking in GA4 Real-Time reports

### 2. Facebook Pixel Setup

For each website:
1. Create Facebook Pixel in Meta Business Manager
2. Copy Pixel ID
3. Replace `YOUR_PIXEL_ID` placeholder in generated code with actual Pixel ID
4. Add Facebook Pixel code to WordPress theme header
5. Verify pixel firing in Facebook Events Manager

### 3. UTM Tracking Setup

1. Review UTM tracking guide for each website
2. Implement UTM parameters on all external links:
   - Social media bio links
   - Email campaign links
   - Paid advertising links
   - Cross-site promotion links
3. Create UTM parameter tracking spreadsheet
4. Monitor UTM performance in GA4

### 4. Custom Events Tracking

Implement custom event tracking for each website based on events list:

{chr(10).join([f"**{domain}**: {', '.join(config['events'])}" for domain, config in results["websites"].items()])}

### 5. Metrics Dashboard Setup

1. Create weekly metrics tracking spreadsheet for each website
2. Use generated dashboard template as starting point
3. Set up data collection workflow:
   - Weekly GA4 data export
   - Form submission tracking
   - Payment system integration
4. Schedule weekly metrics review

## Files Generated

{chr(10).join([f"- {file}" for file in results["files_created"]])}

## Next Steps

1. **WordPress Integration**: Deploy tracking codes to WordPress themes
2. **Testing**: Verify all tracking codes fire correctly
3. **Event Implementation**: Implement custom event tracking
4. **Documentation**: Document site-specific tracking configurations
5. **Training**: Train team on UTM parameter usage and metrics review

## Resources

- [GA4 Documentation](https://developers.google.com/analytics/devguides/collection/ga4)
- [Facebook Pixel Documentation](https://developers.facebook.com/docs/meta-pixel)
- [UTM Parameter Builder](https://ga-dev-tools.google/campaign-url-builder/)
"""


if __name__ == "__main__":
    print("🚀 Starting Batch Analytics Setup...")
    print(f"📊 Processing {len(WEBSITES)} websites...")
    
    results = create_implementation_files()
    
    print(f"✅ Batch analytics setup complete!")
    print(f"📁 Files created: {len(results['files_created'])}")
    print(f"📊 Websites processed: {len(results['websites'])}")
    print(f"\n📄 Summary report: docs/analytics_setup/batch_analytics_setup_summary_{results['timestamp']}.json")
    print(f"📖 Implementation guide: docs/analytics_setup/BATCH_ANALYTICS_IMPLEMENTATION_GUIDE.md")

