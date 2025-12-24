# Metrics Collection Setup Guide

**Generated:** 2025-12-22  
**Purpose:** Configure automated metrics collection for website analytics dashboards  
**Status:** Template implementation ready for API integration

## Overview

The `collect_website_metrics.py` tool provides automated collection and reporting of website metrics from multiple sources. This guide explains how to configure it for production use.

## Data Sources

### 1. Google Analytics 4 (GA4)

**Required Setup:**
1. Create GA4 service account in Google Cloud Console
2. Enable Google Analytics Data API
3. Grant service account access to GA4 properties
4. Download service account JSON key
5. Configure property IDs for each website

**Configuration:**
```python
GA4_PROPERTY_IDS = {
    "crosbyultimateevents.com": "G-XXXXXXXXXX",
    "dadudekc.com": "G-XXXXXXXXXX",
    "freerideinvestor.com": "G-XXXXXXXXXX",
    "houstonsipqueen.com": "G-XXXXXXXXXX",
    "tradingrobotplug.com": "G-XXXXXXXXXX",
}
```

**Metrics Collected:**
- Sessions
- Users
- New Users
- Page Views (lead magnet landing pages)
- Custom Events (lead_magnet_submit, contact_form_submit, booking_click)

### 2. Form Submissions

**Sources:**
- WordPress database (wp_posts, wp_postmeta)
- Form plugin APIs (Contact Form 7, Gravity Forms, etc.)
- Email notification logs

**Configuration:**
- Database connection credentials
- Form plugin API keys
- Form field mappings

**Metrics Collected:**
- Lead Magnet Submits
- Contact Form Submits

### 3. Payment Systems

**Sources:**
- Stripe API (deposits, payments)
- Calendly API (bookings)
- WordPress booking plugins

**Configuration:**
- Stripe API keys
- Calendly API tokens
- Booking plugin database queries

**Metrics Collected:**
- Bookings
- Deposits Paid
- Revenue

## Installation

### 1. Install Dependencies

```bash
pip install google-analytics-data
pip install stripe
pip install requests
```

### 2. Configure Credentials

Create `config/metrics_credentials.json`:

```json
{
  "ga4": {
    "service_account_path": "path/to/service-account-key.json",
    "property_ids": {
      "crosbyultimateevents.com": "G-XXXXXXXXXX",
      "dadudekc.com": "G-XXXXXXXXXX",
      "freerideinvestor.com": "G-XXXXXXXXXX",
      "houstonsipqueen.com": "G-XXXXXXXXXX",
      "tradingrobotplug.com": "G-XXXXXXXXXX"
    }
  },
  "stripe": {
    "api_key": "sk_live_...",
    "webhook_secret": "whsec_..."
  },
  "calendly": {
    "api_token": "..."
  },
  "wordpress": {
    "database": {
      "host": "...",
      "database": "...",
      "user": "...",
      "password": "..."
    }
  }
}
```

### 3. Set Up Automated Collection

**Weekly Cron Job:**
```bash
# Run every Monday at 9 AM to collect previous week's metrics
0 9 * * 1 cd /path/to/websites && python tools/collect_website_metrics.py
```

**Manual Collection:**
```bash
# Collect previous week (default)
python tools/collect_website_metrics.py

# Collect specific week
python tools/collect_website_metrics.py 2025-12-15
```

## Output Files

### JSON Reports
- Location: `docs/metrics/weekly_metrics_YYYY-MM-DD_TIMESTAMP.json`
- Format: Structured JSON with all metrics per website
- Use: Data processing, API integration, historical analysis

### Markdown Dashboards
- Location: `docs/metrics/weekly_dashboard_YYYY-MM-DD_TIMESTAMP.md`
- Format: Human-readable markdown table
- Use: Weekly reviews, team reports, documentation

## Integration Examples

### GA4 API Integration

```python
from google.analytics.data_v1beta import BetaAnalyticsDataClient
from google.analytics.data_v1beta.types import RunReportRequest, DateRange, Dimension, Metric

def collect_ga4_metrics(property_id: str, start_date: str, end_date: str):
    client = BetaAnalyticsDataClient.from_service_account_json(
        "path/to/service-account-key.json"
    )
    
    request = RunReportRequest(
        property=f"properties/{property_id}",
        date_ranges=[DateRange(start_date=start_date, end_date=end_date)],
        dimensions=[Dimension(name="sessionSource")],
        metrics=[
            Metric(name="sessions"),
            Metric(name="totalUsers"),
            Metric(name="newUsers"),
        ],
    )
    
    response = client.run_report(request)
    # Process response...
```

### Stripe API Integration

```python
import stripe

stripe.api_key = "sk_live_..."

def collect_stripe_metrics(start_timestamp: int, end_timestamp: int):
    charges = stripe.Charge.list(
        created={"gte": start_timestamp, "lte": end_timestamp},
        limit=100
    )
    # Process charges...
```

## Next Steps

1. **Configure API Credentials**: Set up GA4, Stripe, and other service accounts
2. **Test Collection**: Run manual collection for a test week
3. **Verify Data**: Check generated reports for accuracy
4. **Set Up Automation**: Configure cron job for weekly collection
5. **Create Alerts**: Set up notifications for significant metric changes

## Troubleshooting

### GA4 API Errors
- Verify service account has Analytics Viewer role
- Check property ID format (G-XXXXXXXXXX)
- Ensure Google Analytics Data API is enabled

### Form Metrics Missing
- Verify database connection credentials
- Check form plugin API documentation
- Review form submission logs

### Payment Metrics Missing
- Verify Stripe API key permissions
- Check webhook configuration
- Review payment system logs

## Resources

- [GA4 Data API Documentation](https://developers.google.com/analytics/devguides/reporting/data/v1)
- [Stripe API Documentation](https://stripe.com/docs/api)
- [Calendly API Documentation](https://developer.calendly.com/api-docs)

