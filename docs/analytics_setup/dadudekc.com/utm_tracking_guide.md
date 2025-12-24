# UTM Tracking Guide - dadudekc.com

## UTM Parameter Conventions

### Required Parameters:
- `utm_source`: Traffic source (instagram, facebook, linkedin, email, website, etc.)
- `utm_medium`: Traffic medium (social, bio, post, story, cpc, hero, footer, newsletter)
- `utm_campaign`: Campaign identifier ("leadmagnet_audit", "homepage_optimization", "automation_focus", etc.)

### Optional Parameters:
- `utm_content`: Creative identifier (cta_primary, cta_secondary, video_01, carousel_02)

## Example URLs:

### Instagram Bio → Lead Magnet:
```
https://dadudekc.com/lead-magnet/?utm_source=instagram&utm_medium=bio&utm_campaign=leadmagnet_audit
```

### Email Newsletter → Homepage:
```
https://dadudekc.com/?utm_source=email&utm_medium=newsletter&utm_campaign=leadmagnet_audit
```

### Facebook Ad → Contact:
```
https://dadudekc.com/contact/?utm_source=facebook&utm_medium=cpc&utm_campaign=leadmagnet_audit&utm_content=ad_01
```

## Tracking Setup:
1. Add UTM parameters to all external links (social media, emails, ads)
2. Track UTM parameters in GA4 under "Acquisition" → "Traffic acquisition"
3. Create custom reports for UTM campaign performance
4. Monitor UTM performance weekly in metrics dashboard
