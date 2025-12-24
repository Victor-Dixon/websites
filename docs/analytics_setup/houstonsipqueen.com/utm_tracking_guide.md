# UTM Tracking Guide - houstonsipqueen.com

## UTM Parameter Conventions

### Required Parameters:
- `utm_source`: Traffic source (instagram, facebook, linkedin, email, website, etc.)
- `utm_medium`: Traffic medium (social, bio, post, story, cpc, hero, footer, newsletter)
- `utm_campaign`: Campaign identifier ("leadmagnet_checklist", "luxury_bartending", "event_planning", etc.)

### Optional Parameters:
- `utm_content`: Creative identifier (cta_primary, cta_secondary, video_01, carousel_02)

## Example URLs:

### Instagram Bio → Lead Magnet:
```
https://houstonsipqueen.com/lead-magnet/?utm_source=instagram&utm_medium=bio&utm_campaign=leadmagnet_checklist
```

### Email Newsletter → Homepage:
```
https://houstonsipqueen.com/?utm_source=email&utm_medium=newsletter&utm_campaign=leadmagnet_checklist
```

### Facebook Ad → Contact:
```
https://houstonsipqueen.com/contact/?utm_source=facebook&utm_medium=cpc&utm_campaign=leadmagnet_checklist&utm_content=ad_01
```

## Tracking Setup:
1. Add UTM parameters to all external links (social media, emails, ads)
2. Track UTM parameters in GA4 under "Acquisition" → "Traffic acquisition"
3. Create custom reports for UTM campaign performance
4. Monitor UTM performance weekly in metrics dashboard
