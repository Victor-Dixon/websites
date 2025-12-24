# Batch Analytics Setup Implementation Guide

**Generated:** 20251222_130311  
**Sites:** 5 websites  
**Status:** Ready for implementation

## Overview

This batch implementation covers GA4, Facebook Pixel, UTM tracking, and metrics dashboard setup for:
- crosbyultimateevents.com
- dadudekc.com
- freerideinvestor.com
- houstonsipqueen.com
- tradingrobotplug.com

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

**crosbyultimateevents.com**: lead_magnet_submit, contact_form_submit, booking_click, phone_click
**dadudekc.com**: lead_magnet_submit, contact_form_submit, booking_click, phone_click
**freerideinvestor.com**: lead_magnet_submit, subscription_form_submit, premium_upgrade_click
**houstonsipqueen.com**: lead_magnet_submit, quote_form_submit, booking_click, phone_click
**tradingrobotplug.com**: waitlist_submit, contact_form_submit

### 5. Metrics Dashboard Setup

1. Create weekly metrics tracking spreadsheet for each website
2. Use generated dashboard template as starting point
3. Set up data collection workflow:
   - Weekly GA4 data export
   - Form submission tracking
   - Payment system integration
4. Schedule weekly metrics review

## Files Generated

- docs\analytics_setup\crosbyultimateevents.com\ga4_tracking_code.html
- docs\analytics_setup\crosbyultimateevents.com\facebook_pixel_code.html
- docs\analytics_setup\crosbyultimateevents.com\utm_tracking_guide.md
- docs\analytics_setup\crosbyultimateevents.com\weekly_metrics_dashboard.md
- docs\analytics_setup\dadudekc.com\ga4_tracking_code.html
- docs\analytics_setup\dadudekc.com\facebook_pixel_code.html
- docs\analytics_setup\dadudekc.com\utm_tracking_guide.md
- docs\analytics_setup\dadudekc.com\weekly_metrics_dashboard.md
- docs\analytics_setup\freerideinvestor.com\ga4_tracking_code.html
- docs\analytics_setup\freerideinvestor.com\facebook_pixel_code.html
- docs\analytics_setup\freerideinvestor.com\utm_tracking_guide.md
- docs\analytics_setup\freerideinvestor.com\weekly_metrics_dashboard.md
- docs\analytics_setup\houstonsipqueen.com\ga4_tracking_code.html
- docs\analytics_setup\houstonsipqueen.com\facebook_pixel_code.html
- docs\analytics_setup\houstonsipqueen.com\utm_tracking_guide.md
- docs\analytics_setup\houstonsipqueen.com\weekly_metrics_dashboard.md
- docs\analytics_setup\tradingrobotplug.com\ga4_tracking_code.html
- docs\analytics_setup\tradingrobotplug.com\facebook_pixel_code.html
- docs\analytics_setup\tradingrobotplug.com\utm_tracking_guide.md
- docs\analytics_setup\tradingrobotplug.com\weekly_metrics_dashboard.md

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
